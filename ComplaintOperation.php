<?php

namespace NW\WebService\References\Operations\Notification;

use Contractors\ContractorsFactory;

class ComplaintOperation extends ReferencesOperation
{
    const TYPE_NEW    = 1;
    const TYPE_CHANGE = 2;

    /**
     * 
     */
    public function doOperation(): array
    {
        $result = [
            'notificationResellersByEmail' => [
                'isSent' => false,
                'Errors' => [],
            ],
            'notificationClientByEmail'   => [
                'isSent' => false,
                'Errors' => '',
            ],
            'notificationClientBySms'     => [
                'isSent'  => false,
                'message' => '',
            ],
        ];

        $data = (array)$this->getRequest('data');
        $errors = $this->checkData($data);
        if(!empty($errors)) {
            $errors_message = implode(', ', $errors);
            \App::i()->renderErroPage($errors_message, 400);
        }

        $contractors = new ContractorsFactory();

        $reseller = $contractors::getResellerById($data['resellerId']);
        if ($reseller->getId() === null) {
            \App::i()->renderErroPage('Reseller not found!', 404);
        }

        $client = $contractors::getClientById($data['clientId']);
        if ($client->getId() === null) {
            \App::i()->renderErroPage('Client not found!', 404);
        }

        $creator = $contractors::getCreatorById($data['creatorId']);
        if ($creator->getId() === null) {
            \App::i()->renderErroPage('Creator not found!', 404);
        }

        $expert = $contractors::getExpertById($data['expertId']);
        if ($expert->getId() === null) {
            \App::i()->renderErroPage('Expert not found!', 404);
        }

        $templateData = $this->fillTemplateData(
            $data, 
            $creator->getFullName(), 
            $expert->getFullName(), 
            $client->getFullName(), 
        );

        $emailResselerFrom = $reseller->getEmail();
        $resellerEmails    = $reseller->getEmailsByStatus(ComplaintStatus::byType[$notificationType]);
        if (!empty($emailResselerFrom) && count($resellerEmails) > 0) {
            $sendResellerErrors = [];
            foreach ($resellerEmails as $email) {
                try{
                    MessagesReseller::sendMessage([
                        0 => [
                               'emailFrom' => $emailResselerFrom,
                               'emailTo'   => $email,
                               'subject'   => __($reseller->getComplaintEmailSubject(), $templateData),
                               'message'   => __($reseller->getComplaintEmailBody(), $templateData),
                        ],
                    ], 
                    $reseller->getId(), 
                    ComplaintStatus::byType[$notificationType]
                    );
                } catch(\Exception $e) {
                    $sendResellerErrors[] = 'Error: ' . $e . ' email: ' . $email
                }
            }  
            if(empty($sendResellerErrors)){
                $result['notificationResellersByEmail']['isSent'] = true;
            } else {
                $result['notificationResellersByEmail']['Errors'] = implode(', ', $sendResellerErrors);
            }
        }

        // Шлём клиентское уведомление, только если произошла смена статуса
        if ($notificationType === self::TYPE_CHANGE && !empty($data['differences']['TO'])) {
            if (!empty($emailResselerFrom) && !empty($client->getEmail())) {
                $sendClientError = '';
                try {
                    MessagesClient::sendMessage([
                        0 => [
                               'emailFrom' => $emailResselerFrom,
                               'emailTo'   => $client->getEmail(),
                               'subject'   => __($client->getComplaintEmailSubject(), $templateData),
                               'message'   => __($client->getComplaintEmailBody(), $templateData),
                        ],
                    ], 
                    $reseller->getId(), 
                    $client->getId(),
                    ComplaintStatus::byType[$notificationType], 
                    $data['differences']['TO']
                    );
                } catch (\Exception $e) {
                    $sendClientError = 'Error: ' . $e . ' email: ' . $email;
                }

                if(empty($sendClientErrors)) {
                    $result['notificationClientByEmail']['isSent'] = true;
                }else {
                    $result['notificationClientByEmail']['Errors'] = $sendClientError;
                }
            }

            if (!empty($client->getMobile())) {
                $error = '';
                $res = NotificationManager::send(
                    $reseller->getId(), 
                    $client->getId(), 
                    ComplaintStatus::byType[$notificationType], 
                    $data['differences']['TO'], 
                    $templateData, 
                    $error
                );
                if ($res) {
                    $result['notificationClientBySms']['isSent'] = true;
                } else if(!empty($error)) {
                    $result['notificationClientBySms']['message'] = $error;
                }
            }
        }

        return $result;
    }

    private function fillTemplateData(
        array $data, 
        string $creatorFullName, 
        string $expertFullName, 
        string $clientFullName, 
    ): array {
        $templateData = [
            'COMPLAINT_ID'       => $data['complaintId']        ?? null,
            'COMPLAINT_NUMBER'   => $data['complaintNumber']    ?? null,
            'CREATOR_ID'         => $data['creatorId']          ?? null,
            'CREATOR_NAME'       => $creatorFullName,
            'EXPERT_ID'          => $data['expertId']           ?? null,
            'EXPERT_NAME'        => $expertFullName,
            'CLIENT_ID'          => $data['clientId']           ?? null,
            'CLIENT_NAME'        => $clientFullName,
            'CONSUMPTION_ID'     => $data['consumptionId']      ?? null,
            'CONSUMPTION_NUMBER' => $data['consumptionNumber']  ?? null,
            'AGREEMENT_NUMBER'   => $data['agreementNumber']    ?? null,
            'DATE'               => $data['date']               ?? null,
            'DIFFERENCES'        => $data['differences']        ?? null,
        ];
        return $templateData;
    }

    private function getDataByRequest($data): array {
        $differences = [
            'FROM' => null,
            'TO'   => null,
        ];
        if (
            (int)$data['notificationType'] ?? null === self::TYPE_CHANGE 
            && !empty($data['differences'] ?? null)
        ) {
            $differences['FROM'] = Events::byCode[(int)$data['differences']['from'] ?? null] ?? null;
            $differences['TO']   = Events::byCode[(int)$data['differences']['to'] ?? null] ?? null;
        }

        $result = [
            'resellerId'        => (int)$data['resellerId']          ?? null,
            'notificationType'  => (int)$data['notificationType']    ?? null,
            'clientId'          => (int)$data['clientId']            ?? null,
            'clientName'        => (int)$data['clientName']          ?? null,
            'creatorId'         => (int)$data['creatorId']           ?? null,
            'creatorName'       => (int)$data['creatorName']         ?? null,
            'expertId'          => (int)$data['expertId']            ?? null,
            'expertName'        => (int)$data['expertName']          ?? null,
            'complaintNumber'   => (string)$data['complaintNumber']  ?? null,
            'complaintId'       => (int)$data['complaintId']         ?? null,
            'consumptionId'     => (int)$data['consumptionId']       ?? null,
            'consumptionNumber' => (string)$data['agreementNumber']  ?? null,
            'date'              => (string)$data['date']             ?? null,
            'agreementNumber'   => (string)$data['agreementNumber']  ?? null,
            'differences'       => $differences,
        ];

        return $result;
    }

    private function checkData(array $data): array {
        $errors = [];
        foreach ($templateData as $key => $tempData) {
            if ($tempData === null) {
                $errors[] = 'Template Data (' . $key . ') is empty!';
            }
        }
        return $errors;
    }

}