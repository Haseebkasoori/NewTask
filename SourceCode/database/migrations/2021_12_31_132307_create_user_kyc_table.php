<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserKycTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_kyc', function (Blueprint $table) {
            $table->id();
            $table->string('profile_image');
            $table->string('utility_bill_image');
            $table->string('cnic_image');
            $table->string('consignment')->default('True');
            $table->enum('status',['Pending','Approved'])->default('True');
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();

        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_kyc');
    }
}
