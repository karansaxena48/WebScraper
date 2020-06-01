<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public $fillable = ['CorporateIdentificationNumber', 'CompanyName', 'CompanyStatus', 'DateofIncorporation',
                        'RegistrationNumber', 'CompanyCategory', 'CompanySubcategory', 'ClassofCompany', 'ROCCode',
                        'NumberofMembers', 'EmailAddress', 'RegisteredOffice', 'Whetherlistedornot', 'DateofLastAGM',
                        'DateofBalancesheet', 'State', 'District', 'City', 'PIN', 'Section', 'Division', 'MainGroup', 'MainClass'];
}
