<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;
use App\Company;
use App\Director;
use App\Repositories\CompanyParserRepository;

class IndustryParserRepository {

    private $companyParserRepository;
    private $baseCompanyUrl = 'http://www.mycorporateinfo.com';

    public function __construct(CompanyParserRepository $companyParserRepository) {
        ini_set('max_execution_time', (60 * 60 * 12));
        ini_set('memory_limit','1024M');
        $this->companyParserRepository = $companyParserRepository;
    }

    public function parse($url) {
        $client = new Client(['http_errors' => false]);

        $pageNo = 1;
        while(1) {
            $response = $client->request('GET', $url.'/page/'.$pageNo);
            $response_status_code = $response->getStatusCode();

            if($response_status_code == 200) {
                $html = $response->getBody()->getContents();
                $dom = HtmlDomParser::str_get_html($html);
                $checkRecord = $dom->find('section[class="test"] h2', 1);
                if($checkRecord && trim($checkRecord->text()) == "No Results Found.") {
                    break;
                }

                $companylist = $dom->find('section[class="test"] table tr');
                foreach($companylist as $company) {
                    if($company->find('th')) continue;
                    $CID = trim($company->find('td', 0)->text());
                    if(Company::where('CorporateIdentificationNumber', $CID)->exists()) continue;
                    $companyUrl = $this->baseCompanyUrl . $company->find('td', 1)->find('a', 0)->href;
                    $data = $this->companyParserRepository->parse($companyUrl);
                    $this->companyParserRepository->saveCompanyData($data);
                }
            }
            $pageNo++;
        }

        return response()->json(['message' => 'success']);
    }

    public function parseAll() {
        $client = new Client(['http_errors' => false]);
        $response = $client->request('GET', $this->baseCompanyUrl . '/industry');
        $response_status_code = $response->getStatusCode();
        $html = $response->getBody()->getContents();
        if($response_status_code == 200) {
            $dom = HtmlDomParser::str_get_html($html);
            $liList = $dom->find('div[class="row"] ul[class="list-group"] li[class="list-group-item"]');
            foreach($liList as $li) {
                $industryLink = $this->baseCompanyUrl . $li->find('a', 0)->href;
                $this->parse($industryLink);
            }
        }

        return response()->json(['message' => 'success']);
    }
}



?>
