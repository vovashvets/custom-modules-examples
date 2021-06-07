<?php

namespace Drupal\lightweight_webform_mailchimp\Services;

use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Exception;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;

/**
 * Class LightweightWebformMailchimpClient.
 *
 * HTTP Client based in Guzzle for connecting to Mailchimp and save contacts.
 *
 * @package Drupal\lightweight_webform_mailchimp\Services
 * @access public
 * @see
 */
class LightweightWebformMailchimpClient {

  use StringTranslationTrait;

  /**
   * Guzzle\Client instance.
   *
   * @var Guzzle\ClientInterface
   */
  protected $lwmHttpClient;

  /**
   * Logger instance.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  protected $lwmLogger;

  /**
   * Messenger instance.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $lwmMessenger;

  /**
   * API Key for the Mailchimp connection.
   *
   * @var string
   */
  protected $lwmAPIKey;

  /**
   * Url for the Mailchimp connection.
   *
   * @var string
   */
  protected $lwmUrl;

  /**
   * List Name for the Mailchimp connection.
   *
   * @var string
   */
  protected $lwmListName;

  /**
   * Tag Name for the Mailchimp connection.
   *
   * @var string
   */
  protected $lwmTagName;

  /**
   * E Mail for the Mailchimp connection.
   *
   * @var string
   */
  protected $lwmFieldMail;

  /**
   * First Name for the Mailchimp connection.
   *
   * @var string
   */
  protected $lwmFirstName;

  /**
   * Last Name for the Mailchimp connection.
   *
   * @var string
   */
  protected $lwmLastName;

  /**
   * Constructs a LightweightWebformMailchimpClient.
   *
   * @param \GuzzleHttp\ClientInterface $http_client
   *   The http client service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The Logger Factory service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(ClientInterface $http_client, LoggerChannelFactoryInterface $logger_factory, MessengerInterface $messenger) {
    // Load services passed as arguments.
    $this->lwmHttpClient = $http_client;
    $this->lwmLogger = $logger_factory->get('lightweight_webform_mailchimp');
    $this->lwmMessenger = $messenger;
  }

  /**
   * Load the values obtained from the webform submission and config.
   *
   * @param array $values
   *   Array of data from config and webform submission.
   */
  public function setInitialValues(array $values) {
    $this->lwmAPIKey = $values['api_key'];
    $this->lwmListName = $values['list_name'];
    $this->lwmTagName = $values['tag_name'];
    $this->lwmFieldMail = $values['field_email'];
    $this->lwmFirstName = $values['first_name'];
    $this->lwmLastName = $values['last_name'];
    $this->lwmUrl = 'https://' . (explode('-', $this->lwmAPIKey, 2))[1] . '.' . 'api.mailchimp.com/3.0/';
  }

  /**
   * Try an initial connection before storage operations.
   *
   * @return string
   *   A response code from Mailchimp.
   *
   * @throws \Exception
   *   Throws up a generic Exception if no connection was possible.
   * @throws \GuzzleHttp\Exception\GuzzleException
   *  Throws up a base GuzzleException if there was a generic error.
   * @throws \GuzzleHttp\Exception\RequestException
   *   Throws up a Guzzle RequestException in the event of a networking error.
   * @throws \GuzzleHttp\Exception\ClientException
   *   Throws up a Guzzle ClientException from 400 level errors.
   * @throws GuzzleHttp\Exception\BadResponseException
   *  Throws up a Guzzle BadResponseException from a response level error.
   * @throws \GuzzleHttp\Exception\ServerException
   *  Throws up a Guzzle ServerException from 500 level errors.
   */
  public function getInitialPing() {

    try {
      $request_initial = $this->lwmHttpClient->get($this->lwmUrl, [
        'auth' => ['anystring', $this->lwmAPIKey],
        'http_errors' => TRUE,
      ]);
      $response_initial = json_decode($request_initial->getBody(), TRUE);
      $code_initial = $request_initial->getStatusCode();

      if ($code_initial == 200) {
        $this->lwmLogger->notice(t('Response code 200, connected to account name: @account'), ['@account' => $response_initial['account_name']]);
      }
      else {
        throw  new Exception(t('Mailchimp returns a error code: @code.'), ['@code' => $code_initial]);
      }
    }
    catch (ServerException $e) {
      $code_initial = 'xxx';
      $this->lwmLogger->error(t('ServerException - Error connecting with Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (ClientException $e) {
      $code_initial = 'xxx';
      $this->lwmLogger->error(t('ClientException - Error connecting with Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (BadResponseException $e) {
      $code_initial = 'xxx';
      $this->lwmLogger->error(t('BadResponseException - Error connecting with Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (RequestException $e) {
      $code_initial = 'xxx';
      $this->lwmLogger->error(t('Request Exception - Error connecting with Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (GuzzleException $e) {
      $code_initial = 'xxx';
      $this->lwmLogger->error(t('GuzzleException - Error connecting with Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (Exception $e) {
      $code_initial = 'xxx';
      $this->lwmLogger->error(t('Exception - Error connecting with Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    } finally {
      return $code_initial;
    }
  }

  /**
   * Get all the lists related with an Mailchimp account.
   *
   * @return array
   *  Array of lists with all related data.
   *
   * @throws \Exception
   *   Throws up a generic Exception if no connection was possible.
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Throws up a base GuzzleException if there was a generic error.
   * @throws \GuzzleHttp\Exception\RequestException
   *    Throws up a Guzzle RequestException in the event of a networking error.
   * @throws \GuzzleHttp\Exception\ClientException
   *    Throws up a Guzzle ClientException from 400 level errors.
   * @throws GuzzleHttp\Exception\BadResponseException
   *   Throws up a Guzzle BadResponseException from a response level error.
   * @throws \GuzzleHttp\Exception\ServerException
   *   Throws up a Guzzle ServerException from 500 level errors.
   */
  public function getListsFromAccount() {
    try {
      $request_lists = $this->lwmHttpClient->get($this->lwmUrl . 'lists', [
        'auth' => ['anystring', $this->lwmAPIKey],
        'http_errors' => TRUE,
      ]);
      $response_lists = json_decode($request_lists->getBody(), TRUE);
    }
    catch (ServerException $e) {
      $this->lwmLogger->error(t('ServerException - Error getting lists from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (ClientException $e) {
      $this->lwmLogger->error(t('ClientException - Error getting lists from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (BadResponseException $e) {
      $this->lwmLogger->error(t('BadResponseException - Error getting lists from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (RequestException $e) {
      $this->lwmLogger->error(t('Request Exception - Error getting lists from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (GuzzleException $e) {
      $this->lwmLogger->error(t('GuzzleException - Error getting lists from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (Exception $e) {
      $this->lwmLogger->error(t('Exception - Error getting lists from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    } finally {
      return $response_lists['lists'];
    }
  }

  /**
   * Gets the list / audience ID from an array of Mailchimp lists.
   *
   * @return string
   *   ID of the selected list / audience.
   */
  public function getSelectedList() {

    $list_id = 'NULL';

    foreach ($this->getListsFromAccount() as $position => $list) {
      if ($list['name'] === $this->lwmListName) {
        $list_id = $list['id'];
      }
    }
    return $list_id;
  }

  /**
   * Loads a new contact in a Mailchimp List / Audience.
   *
   * @return \Drupal\lightweight_webform_mailchimp\Services\LightweightWebformMailchimpClient
   *  The Object context itself for chaining methods.
   *
   * @throws \Exception
   *   Throws up a generic Exception if no connection was possible.
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Throws up a base GuzzleException if there was a generic error.
   * @throws \GuzzleHttp\Exception\RequestException
   *   Throws up a Guzzle RequestException in the event of a networking error.
   * @throws \GuzzleHttp\Exception\ClientException
   *   Throws up a Guzzle ClientException from 400 level errors.
   * @throws GuzzleHttp\Exception\BadResponseException
   *   Throws up a Guzzle BadResponseException from a response level error.
   * @throws \GuzzleHttp\Exception\ServerException
   *   Throws up a Guzzle ServerException from 500 level errors.
   */
  public function setNewContact() {

    // Builds the Body for the request.
    $serialized_body = json_encode([
      'email_address' => $this->lwmFieldMail,
      'status' => 'subscribed',
      'merge_fields' => [
        'FNAME' => $this->lwmFirstName,
        'LNAME' => $this->lwmLastName,
      ],
    ]);

    try {
      // Executes the request.
      $this->lwmHttpClient->post($this->lwmUrl . 'lists/' . $this->getSelectedList() . '/members', [
        'auth' => ['anystring', $this->lwmAPIKey],
        'body' => $serialized_body,
        'http_errors' => TRUE,
        'headers' => [
          'Content-Type' => 'application/hal+json',
        ],
      ]);
    }
    catch (ServerException $e) {
      $this->lwmLogger->error(t('ServerException - Error setting a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (ClientException $e) {
      $this->lwmLogger->error(t('ClientException - Error setting a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (BadResponseException $e) {
      $this->lwmLogger->error(t('BadResponseException - Error setting a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (RequestException $e) {
      $this->lwmLogger->error(t('Request Exception - Error setting a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (GuzzleException $e) {
      $this->lwmLogger->error(t('GuzzleException - Error setting a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    }
    catch (Exception $e) {
      $this->lwmLogger->error(t('Exception - Error setting a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
      $this->lwmMessenger->addMessage(t('We are experiencing technical problems, please try again after a few minutes.'), 'error');
    } finally {
      return $this;
    }
  }

  /**
   * Gets a segment ID assigned to a tag.
   *
   * @return string
   *   The segment ID from Mailchimp.
   *
   * @throws \Exception
   *   Throws up a generic Exception if no connection was possible.
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Throws up a base GuzzleException if there was a generic error.
   * @throws \GuzzleHttp\Exception\RequestException
   *   Throws up a Guzzle RequestException in the event of a networking error.
   * @throws \GuzzleHttp\Exception\ClientException
   *   Throws up a Guzzle ClientException from 400 level errors.
   * @throws GuzzleHttp\Exception\BadResponseException
   *   Throws up a Guzzle BadResponseException from a response level error.
   * @throws \GuzzleHttp\Exception\ServerException
   *   Throws up a Guzzle ServerException from 500 level errors.
   */
  public function getSegmentIDTag() {
    // Gets the segment ID assigned to a tag.
    // @see https://mailchimp.com/developer/guides/how-to-use-tags/
    $segment_id = 'NULL';
    try {
      $request_segments = $this->lwmHttpClient->get($this->lwmUrl . 'lists/' . $this->getSelectedList() . '/segments', [
        'auth' => ['anystring', $this->lwmAPIKey],
        'http_errors' => TRUE,
      ]);
      $response_segments = (json_decode($request_segments->getBody(), TRUE))['segments'];
      foreach ($response_segments as $position => $segment) {
        // For Mailchimp tag and segment are the same entity, changing only the 'type' property.
        if ($segment['name'] == $this->lwmTagName) {
          $segment_id = $segment['id'];
        }
      }
    }
    catch (ServerException $e) {
      $this->lwmLogger->error(t('ServerException - Error getting a Segment ID from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (ClientException $e) {
      $this->lwmLogger->error(t('ClientException - Error getting a Segment ID from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (BadResponseException $e) {
      $this->lwmLogger->error(t('BadResponseException - Error getting a Segment ID from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (RequestException $e) {
      $this->lwmLogger->error(t('Request Exception - Error getting a Segment ID from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (GuzzleException $e) {
      $this->lwmLogger->error(t('GuzzleException - Error getting a Segment ID from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (Exception $e) {
      $this->lwmLogger->error(t('Exception - Error getting a Segment ID from Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    } finally {
      return $segment_id;
    }
  }

  /**
   * Set the selected tag to a recently saved contact in Mailchimp.
   *
   * @return \Drupal\lightweight_webform_mailchimp\Services\LightweightWebformMailchimpClient
   *   The Object context itself for chaining methods.
   *
   *
   * @throws \Exception
   *   Throws up a generic Exception if no connection was possible.
   * @throws \GuzzleHttp\Exception\GuzzleException
   *   Throws up a base GuzzleException if there was a generic error.
   * @throws \GuzzleHttp\Exception\RequestException
   *   Throws up a Guzzle RequestException in the event of a networking error.
   * @throws \GuzzleHttp\Exception\ClientException
   *   Throws up a Guzzle ClientException from 400 level errors.
   * @throws GuzzleHttp\Exception\BadResponseException
   *   Throws up a Guzzle BadResponseException from a response level error.
   * @throws \GuzzleHttp\Exception\ServerException
   *   Throws up a Guzzle ServerException from 500 level errors.
   */
  public function setTagToNewContact() {

    // Builds the Body for the request.
    $serialized_body_segment = json_encode(['email_address' => $this->lwmFieldMail]);
    try {
      // Builds the request.
      $this->lwmHttpClient->post($this->lwmUrl . 'lists/' . $this->getSelectedList() . '/segments/' . $this->getSegmentIDTag() . '/members', [
        'auth' => ['anystring', $this->lwmAPIKey],
        'body' => $serialized_body_segment,
        'http_errors' => TRUE,
        'headers' => [
          'Content-Type' => 'application/hal+json',
        ],
      ]);
    }
    catch (ServerException $e) {
      $this->lwmLogger->error(t('ServerException - Error setting a Tag ID to a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (ClientException $e) {
      $this->lwmLogger->error(t('ClientException - Error setting a Tag ID to a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (BadResponseException $e) {
      $this->lwmLogger->error(t('BadResponseException - Error setting a Tag ID to a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (RequestException $e) {
      $this->lwmLogger->error(t('Request Exception - Error setting a Tag ID to a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (GuzzleException $e) {
      $this->lwmLogger->error(t('GuzzleException - Error setting a Tag ID to a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    }
    catch (Exception $e) {
      $this->lwmLogger->error(t('Exception - Error setting a Tag ID to a new contact in Mailchimp, error: @error'), ['@error' => $e->getMessage()]);
    } finally {
      return $this;
    }
  }

}
