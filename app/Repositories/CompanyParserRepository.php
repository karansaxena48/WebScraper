<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;
use App\Company;
use App\Director;

class CompanyParserRepository {

    private $modules = ['companyinformation', 'contactdetails', 'listingandannualcomplaincedetails', 'otherinformation'];
    private $data = [];

    public function parse($url) {
        $client = new Client(['http_errors' => false]);
        $response = $client->request('GET', $url);
        $response_status_code = $response->getStatusCode();
        $html = $response->getBody()->getContents();

        if($response_status_code == 200) {
            $dom = HtmlDomParser::str_get_html($html);
            foreach($this->modules as $module) {
                $this->data = array_merge($this->data, $this->getCompanyInformation($module, $dom));
            }
            $this->data['directors'] = $this->getDirectors($dom);
        }

        return $this->data;
    }

    private function getCompanyInformation($module, $dom) {
        $companyinformation = $dom->find("#$module table tr");

        $data = [];
        foreach($companyinformation as $info) {
            $key = str_replace(' ', '', $info->find('td', 0)->find('b', 0)->text());
            $tempval = trim($info->find('td', 1));
            $valueArr = explode('See other', $info->find('td', 1)->text());
            $value = trim($valueArr[0]);

            if($key == 'Age(DateofIncorporation)') {
                $key = 'DateofIncorporation';
                $start = strpos($value,"(");
                $end = strpos($value,")");
                $value = substr($value, $start+1, $end-$start-1);
            }
            $key = explode('(', $key)[0];
            $data[$key] = $value;
        }

        return $data;
    }

    private function getDirectors($dom) {
        $directorsinfo = $dom->find("#directors table tbody tr");

        $data = [];
        foreach($directorsinfo as $key=>$info) {
            $keysArr = $info->find('th');
            foreach($keysArr as $index=>$val) {
                $keys[$index] = str_replace(' ', '', $val->text());
            };

            $tempdata = [];
            $valArr = $info->find('td');
            foreach($valArr as $index=>$val) {
                $tempdata[$keys[$index]] = trim($val->text());
            }
            if(!empty($tempdata)) $data[] = $tempdata;
        }

        return $data;
    }

    public function saveCompanyData($parsedData) {
        if(empty($parsedData['CorporateIdentificationNumber'])) {
            return response()->json(['message' => 'no data']);
        }

        $keys = ['CorporateIdentificationNumber', 'CompanyName', 'CompanyStatus', 'DateofIncorporation',
        'RegistrationNumber', 'CompanyCategory', 'CompanySubcategory', 'ClassofCompany', 'ROCCode',
        'NumberofMembers', 'EmailAddress', 'RegisteredOffice', 'Whetherlistedornot', 'DateofLastAGM',
        'DateofBalancesheet', 'State', 'District', 'City', 'PIN', 'Section', 'Division', 'MainGroup', 'MainClass'];

        $data = [];
        foreach($keys as $key) {
            $data[$key] = $parsedData[$key] ?? '';
        }

        $company = Company::where('CorporateIdentificationNumber', $parsedData['CorporateIdentificationNumber']);
        if($company->exists()) {
            Company::where('id', $company->get()[0]->id)->update($data);
            $this->saveCompanyDirectorsData($company->get()[0]->id, $parsedData['directors']);
        } else {
            $this->saveCompanyDirectorsData(Company::create($data)->id, $parsedData['directors']);
        }

        return response()->json(['message' => 'success']);
    }

    private function saveCompanyDirectorsData($company_id, $directors) {
        $keys = ['DirectorIdentificationNumber', 'Name', 'Designation', 'DateofAppointment'];

        foreach($directors as $director) {
            $data = ['company_id' => $company_id];
            foreach($keys as $key) {
                $data[$key] = $director[$key] ?? '';
            }

            $director = Director::where(['company_id' => $company_id,
                            'DirectorIdentificationNumber' => $data['DirectorIdentificationNumber']]);
            if($director->exists()) {
                Director::where('id', $director->get()[0]->id)->update($data);
            } else {
                Director::create($data);
            }
        }
        return;
    }
}



?>
