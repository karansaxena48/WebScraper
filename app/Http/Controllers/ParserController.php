<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CompanyParserRepository;
use App\Repositories\IndustryParserRepository;

class ParserController extends Controller
{
    public function parseCompany(CompanyParserRepository $companyParserRepository, Request $request) {
        $url = $request->url;
        $data = $companyParserRepository->parse($url);
        return $companyParserRepository->saveCompanyData($data);
    }

    public function parseCompaniesOfIndustry(IndustryParserRepository $industryParserRepository, Request $request) {
        $url = $request->url;
        return $industryParserRepository->parse($url);
    }

    public function parseAllCompaniesOfIndustry(IndustryParserRepository $industryParserRepository) {
        return $industryParserRepository->parseAll();
    }
}
