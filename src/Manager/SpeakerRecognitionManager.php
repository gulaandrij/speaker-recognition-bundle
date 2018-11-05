<?php

namespace Lavulator\SpeakerRecognitionBundle\Manager;

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class SpeakerRecognitionManager.
 */
class SpeakerRecognitionManager
{
    /**
     * @var string
     */
    protected $ocpApimSubscriptionKey1;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * SpeakerRecognitionManager constructor.
     *
     * @param string $endpoint
     * @param string $ocpApimSubscriptionKey1
     */
    public function __construct($endpoint, $ocpApimSubscriptionKey1)
    {
        $this->endpoint = $endpoint;
        $this->ocpApimSubscriptionKey1 = $ocpApimSubscriptionKey1;
    }

    /* Identification Profile */

    /**
     * @param File   $file
     * @param string $identificationProfileId
     * @param string $shortAudio
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createEnrollment(File $file, $identificationProfileId, $shortAudio = 'true')
    {
        $response = $this->send('/identificationProfiles/'.$identificationProfileId.'/enroll', 'POST', ['shortAudio' => $shortAudio], $file);

        return $response;
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function createProfile()
    {
        $content = '{ "locale":"en-us" }';
        $contentType = 'application/json';
        $response = $this->send('/identificationProfiles', 'POST', [], $content, $contentType);

        return $response;
    }

    /**
     * @param $identificationProfileId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteProfile($identificationProfileId)
    {
        $response = $this->send('/identificationProfiles/'.$identificationProfileId, 'DELETE');

        return $response;
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getAllProfiles()
    {
        return $this->send('/identificationProfiles');
    }

    /**
     * @param $identificationProfileId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getProfile($identificationProfileId)
    {
        $contentType = 'application/json';
        $response = $this->send('/identificationProfiles/'.$identificationProfileId, 'GET', [], null, $contentType);

        return $response;
    }

    /**
     * @param $identificationProfileId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function resetEnrollments($identificationProfileId)
    {
        $response = $this->send('/identificationProfiles/'.$identificationProfileId.'/reset', 'POST');

        return $response;
    }

    /* Speaker Recognition */

    /**
     * @param $operationId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getOperationStatus($operationId)
    {
        return $this->send('/operations/'.$operationId);
    }

    /**
     * @param File $file
     * @param null $identificationProfileIds
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function identification(File $file, $identificationProfileIds = null)
    {
        $parameters = ['shortAudio' => 'true', 'identificationProfileIds' => $identificationProfileIds];

        return $this->send('/identify', 'POST', $parameters, $file);
    }

    /**
     * @param $file
     * @param null $verificationProfileId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verification($file, $verificationProfileId = null)
    {
        $parameters = ['verificationProfileId' => $verificationProfileId];

        return $this->send('/verify', 'POST', $parameters, $file);
    }

    /* Verification Phrase */

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listAllSupportedVerificationPhrases()
    {
        $parameters = ['locale' => 'en-us'];

        return $this->send('/verificationPhrases', 'GET', $parameters);
    }

    /* Verification Profile */

    /**
     * @param File $file
     * @param $verificationProfileId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verificationCreateEnrollment(File $file, $verificationProfileId)
    {
        return $this->send('/verificationProfiles/'.$verificationProfileId.'/enroll', 'POST', [], $file);
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verificationCreateProfile()
    {
        $content = '{ "locale":"en-us" }';
        $contentType = 'application/json';

        return $this->send('/verificationProfiles', 'POST', [], $content, $contentType);
    }

    /**
     * @param $verificationProfileId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verificationDeleteProfile($verificationProfileId)
    {
        return $this->send('/verificationProfiles/'.$verificationProfileId, 'DELETE');
    }

    /**
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verificationGetAllProfiles()
    {
        $parameters = ['locale' => 'en-us'];

        return $this->send('/verificationProfiles', 'GET', $parameters);
    }

    /**
     * @param $verificationProfileId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verificationGetProfile($verificationProfileId)
    {
        $contentType = 'application/json';

        return $this->send('/verificationProfiles/'.$verificationProfileId, 'GET', [], null, $contentType);
    }

    /**
     * @param $identificationProfileId
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function verificationResetEnrollments($identificationProfileId)
    {
        return $this->send('/verificationProfiles/'.$identificationProfileId.'/reset', 'POST');
    }

    /**
     * send request to speaker recognition API (Microsoft Cognitive Services).
     *
     * @param string $endpoint
     * @param string $method
     * @param array  $parameters
     * @param null   $file
     * @param string $contentType
     *
     * @return mixed
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function send($endpoint, $method = 'GET', $parameters = [], $file = null, $contentType = 'application/octet-stream')
    {
        $base_uri = $this->endpoint.$endpoint;
        $client = new Client();
        $config['headers'] = [
            'Content-Type' => $contentType,
            'Ocp-Apim-Subscription-Key' => $this->ocpApimSubscriptionKey1,
        ];
        $config['query'] = $parameters;
        if (null !== $file && 'application/octet-stream' === $contentType) {
            $config['body'] = \file_get_contents($file->getPathName());
        } else {
            $config['body'] = $file;
        }

        $response = $client->request($method, $base_uri, $config);
        $answer = $response->getBody();

        if ('' == $answer) { //HTTP CODE 202
            $headers = $response->getHeaders();
            if (isset($headers['Operation-Location'])) {
                $operationUrl = $response->getHeaders()['Operation-Location'][0];
                if ($operationUrl) {
                    $operationIdArray = \explode('/', $operationUrl);
                    $operationId = $operationIdArray[6];
                    $answer = \json_encode(['operationId' => $operationId]);
                }
            }
        }

        return \json_decode($answer, true);
    }
}
