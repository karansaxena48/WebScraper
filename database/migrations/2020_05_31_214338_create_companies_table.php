<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('CorporateIdentificationNumber')->unique();
            $table->string('CompanyName')->nullable();
            $table->string('CompanyStatus')->nullable();
            $table->string('DateofIncorporation')->nullable();
            $table->string('RegistrationNumber')->nullable();
            $table->string('CompanyCategory')->nullable();
            $table->string('CompanySubcategory')->nullable();
            $table->string('ClassofCompany')->nullable();
            $table->string('ROCCode')->nullable();
            $table->string('NumberofMembers')->nullable();
            $table->string('EmailAddress')->nullable();
            $table->string('RegisteredOffice')->nullable();
            $table->string('Whetherlistedornot')->nullable();
            $table->string('DateofLastAGM')->nullable();
            $table->string('DateofBalancesheet')->nullable();
            $table->string('State')->nullable();
            $table->string('District')->nullable();
            $table->string('City')->nullable();
            $table->string('PIN')->nullable();
            $table->string('Section')->nullable();
            $table->string('Division')->nullable();
            $table->string('MainGroup')->nullable();
            $table->string('MainClass')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
