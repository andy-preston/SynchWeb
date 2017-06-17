<?php

use DHL\Entity\GB\ShipmentResponse;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Datatype\GB\Piece;

use DHL\Entity\GB\BookPURequest;
use DHL\Entity\GB\BookPUResponse;

use DHL\Entity\GB\CancelPURequest;
use DHL\Entity\GB\CancelPUResponse;

use DHL\Entity\GB\KnownTrackingRequest as Tracking;
use DHL\Entity\GB\TrackingResponse as TrackingResponse;

use DHL\Client\Web as WebserviceClient;


class DHL {
    
    public $region = 'EU';

    public $_country_codes = array(
        'United Kingdom' => 'GB',
        'France' => 'FR',
        'Germany' => 'DE',
    );

    function __construct($user, $password) {
        define('DHL_API_DIR', dirname(__FILE__).'/../lib/DHL-API/');
        require_once(DHL_API_DIR . 'vendor/autoloadManager/autoloadManager.php');

        $scanOption = autoloadManager::SCAN_ONCE;
        $autoloadDir = sys_get_temp_dir() . '/dhl-api-autoload.php';

        $autoloadManager = new AutoloadManager($autoloadDir, $scanOption);
        $autoloadManager->addFolder(DHL_API_DIR . 'vendor');
        $autoloadManager->addFolder(DHL_API_DIR . 'DHL');
        $autoloadManager->register();

        $this->_user = $user;
        $this->_password = $password;
    }



    function get_tracking_info($options) {
        if (!array_key_exists('AWB', $options)) return;

        $request = new Tracking();
        $request->SiteID = $this->_user;
        $request->Password = $this->_password;
        $request->MessageReference = '12345678901234567890' . (string)time();
        $request->MessageTime = date('c');
        $request->LanguageCode = 'en';
        $request->AWBNumber = $options['AWB'];
        $request->LevelOfDetails = array_key_exists('LAST_ONLY', $options) ? 'LAST_CHECK_POINT_ONLY' : 'ALL_CHECK_POINTS';
        $request->PiecesEnabled = 'S';

        $client = new WebserviceClient();
        $xml = $client->call($request);

        $xml = simplexml_load_string(str_replace('req:', '', $xml));
        return $xml;
    }


    function create_awb($options) {
        $shipment = new ShipmentRequest();
        $shipment->SiteID = $this->_user;
        $shipment->Password = $this->_password;

        $shipment->MessageTime = date('c');
        $shipment->MessageReference = (string)rand(100000000000000000,999999999999999999).'0000000000';
        $shipment->RegionCode = $this->region;
        $shipment->RequestedPickupTime = 'Y';
        $shipment->LanguageCode = 'en';
        $shipment->PiecesEnabled = 'Y';
        $shipment->Billing->ShippingPaymentType = $options['payee'];
        $shipment->Billing->ShipperAccountNumber = $options['accountnumber'];
        if ($options['payee'] != 'S') {
            $shipment->Billing->BillingAccountNumber = $options['accountnumber'];
        }
        $shipment->Billing->DutyPaymentType = 'R';

        $shipment->Dutiable->DeclaredValue = $options['declaredvalue'];
        $shipment->Dutiable->DeclaredCurrency = 'GBP';

        $shipment->Consignee->CompanyName = $options['receiver']['company'];
        foreach (split("\n", $options['receiver']['address']) as $l) {
            if ($l) $shipment->Consignee->addAddressLine($l);
        }
        $shipment->Consignee->City = $options['receiver']['city'];
        $shipment->Consignee->PostalCode = $options['receiver']['postcode'];
        $shipment->Consignee->CountryName = $options['receiver']['country'];
        $shipment->Consignee->CountryCode = $this->_country_codes[$options['receiver']['country']];
        $shipment->Consignee->Contact->PersonName = $options['receiver']['name'];
        $shipment->Consignee->Contact->PhoneNumber = $options['receiver']['phone'];
        $shipment->Consignee->Contact->Email = $options['receiver']['email'];
        
        $shipment->ShipmentDetails->NumberOfPieces = sizeof($options['pieces']);
        $shipment->ShipmentDetails->WeightUnit = 'K';
        $shipment->ShipmentDetails->GlobalProductCode = $options['service'];
        $shipment->ShipmentDetails->Date = date('Y-m-d');
        $shipment->ShipmentDetails->Contents = $options['description'];
        $shipment->ShipmentDetails->DimensionUnit = 'C';
        $shipment->ShipmentDetails->CurrencyCode = 'GBP';
        $shipment->ShipmentDetails->DoorTo = 'DD';
        // $shipment->ShipmentDetails->IsDutiable = 'Y';


        $weight = 0;
        foreach ($options['pieces'] as $i => $d) {
            $weight += $d['weight'];

            $piece = new Piece();
            $piece->PieceID = ($i + 1);
            $piece->Weight = $d['weight'];
            $piece->Width = $d['width'];
            $piece->Height = $d['height'];
            $piece->Depth = $d['depth'];
            $shipment->ShipmentDetails->addPiece($piece);
        }

        $shipment->ShipmentDetails->Weight = $weight;

        $shipment->Shipper->ShipperID = (string)rand(10000000,9999999);
        $shipment->Shipper->CompanyName = $options['sender']['company'];
        foreach (split("\n", $options['sender']['address']) as $l) {
            if ($l) $shipment->Shipper->addAddressLine($l);
        }
        $shipment->Shipper->City = $options['sender']['city'];
        $shipment->Shipper->PostalCode = $options['sender']['postcode'];
        $shipment->Shipper->CountryCode = $this->_country_codes[$options['sender']['country']];
        $shipment->Shipper->CountryName = $options['sender']['country'];
        $shipment->Shipper->Contact->PersonName = $options['sender']['name'];
        $shipment->Shipper->Contact->PhoneNumber = $options['sender']['phone'];
        $shipment->Shipper->Contact->Email = $options['sender']['email'];

        $shipment->LabelImageFormat = 'PDF';

        // echo $shipment->toXML();

        $client = new WebserviceClient('staging');
        $xml = $client->call($shipment);

        // echo $xml;

        $response = new ShipmentResponse();
        $response->initFromXML($xml);
        // echo $response->toXML();

        return array(
            'awb' => $response->AirwayBillNumber,
            'label' => $response->LabelImage->OutputImage,
            'weight' => $response->ChargeableWeight,
            'pieces' => sizeof($options['pieces']),
        );
    }


    function request_pickup($options) {
        $pickup = new BookPURequest();
        $pickup->SiteID = $this->_user;
        $pickup->Password = $this->_password;

        $pickup->MessageTime = date('c');
        $pickup->MessageReference = (string)rand(100000000000000000,999999999999999999).'0000000000';
        $pickup->RegionCode = $this->region;

        $pickup->Requestor->AccountType = 'D';
        $pickup->Requestor->AccountNumber = $options['accountnumber'];
        $pickup->Requestor->CompanyName = $options['requestor']['company'];
        $pickup->Requestor->RequestorContact->PersonName = $options['requestor']['name'];
        $pickup->Requestor->RequestorContact->Phone = $options['requestor']['phone'];
        $pickup->Requestor->CompanyName = $options['requestor']['company'];

        $pickup->Place->LocationType = 'B';
        $pickup->Place->CompanyName = $options['requestor']['company'];
        $lines = split("\n", $options['requestor']['address']);
        $pickup->Place->Address1 = $lines[0];
        if (sizeof($lines) > 1) {
            if ($lines[1]) $pickup->Place->Address2 = $lines[1];
        }
        $pickup->Place->City = $options['requestor']['city'];
        $pickup->Place->PostalCode = $options['requestor']['postcode'];
        $pickup->Place->CountryCode = $this->_country_codes[$options['requestor']['country']];
        $pickup->Place->PackageLocation = $options['packagelocation'];

        $pickup->Pickup->PickupDate = $options['pickupdate'];
        $pickup->Pickup->ReadyByTime = $options['readyby'];
        $pickup->Pickup->CloseTime = $options['closetime'];

        $pickup->PickupContact->PersonName = $options['requestor']['name'];
        $pickup->PickupContact->Phone = $options['requestor']['phone'];

        $pickup->ShipmentDetails->AWBNumber = $options['awbnumber'];
        $pickup->ShipmentDetails->NumberOfPieces = $options['pieces'];
        $pickup->ShipmentDetails->WeightUnit = 'K';
        $pickup->ShipmentDetails->Weight = $options['weight'];
        $pickup->ShipmentDetails->DoorTo = 'DD';

        // echo $pickup->toXML();

        $client = new WebserviceClient('staging');
        $xml = $client->call($pickup);

        // echo $xml;

        $response = new BookPUResponse();
        $response->initFromXML($xml);
        // echo $response->toXML();

        return array(
            'confirmationnumber' => $response->ConfirmationNumber,
            'readybytime' => $response->ReadyByTime,
            'callintime' => $response->CallInTime,
        );
    }


    function cancel_pickup($options) {
        $cancelpickup = new CancelPURequest();
        $cancelpickup->SiteID = $this->_user;
        $cancelpickup->Password = $this->_password;

        $cancelpickup->MessageTime = date('c');
        $cancelpickup->MessageReference = (string)rand(100000000000000000,999999999999999999).'0000000000';
        $cancelpickup->RegionCode = $this->region;

        $cancelpickup->RequestorName = $options['name'];
        $cancelpickup->CountryCode = $this->_country_codes[$options['country']];
        $cancelpickup->ConfirmationNumber = $options['confirmationnumber'];
        $cancelpickup->Reason = '007';

        $cancelpickup->PickupDate = $options['pickupdate'];
        $cancelpickup->CancelTime = date('H:m');
        
        // echo $cancelpickup->toXML();

        $client = new WebserviceClient('staging');
        $xml = $client->call($cancelpickup);

        $response = new CancelPUResponse();
        $response->initFromXML($xml);
        // echo $response->toXML();

        return array();
    }


    function get_available_countries() {
        return array_keys($this->_country_codes);
    }

}
