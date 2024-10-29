<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes for the given columns in each table
        Schema::table('astrologer_categories', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('astrologer_stories', function (Blueprint $table) {
            $table->index('astrologerId');
        });
    
        Schema::table('astromall_products', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('banners', function (Blueprint $table) {
            $table->index('bannerImage');
        });
    
        Schema::table('banner_types', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('blockastrologer', function (Blueprint $table) {
            $table->index('astrologerId');
        });
    
        Schema::table('blockuserreview', function (Blueprint $table) {
            $table->index('reviewId');
        });
    
        Schema::table('blogs', function (Blueprint $table) {
            $table->index('title');
        });
    
        Schema::table('blog_categories', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('callaudio', function (Blueprint $table) {
            $table->index('callId');
        });
    
        Schema::table('callrequest', function (Blueprint $table) {
            $table->index('sid');
            $table->index('sid1');
        });
    
        Schema::table('cdemo', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('chatrequest', function (Blueprint $table) {
            $table->index('chatId');
        });
    
        Schema::table('chats', function (Blueprint $table) {
            $table->index('senderName');
        });
    
        Schema::table('commissions', function (Blueprint $table) {
            $table->index('commissionTypeId');
        });
    
        Schema::table('commission_types', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('coupons', function (Blueprint $table) {
            $table->index('name');
            $table->index('couponCode');
        });
    
        Schema::table('dailyhoroscope', function (Blueprint $table) {
            $table->index('category');
            $table->index('horoscopeSignId');
            $table->index('horoscopeDate');
        });
    
        Schema::table('dailyhoroscopeinsight', function (Blueprint $table) {
            $table->index('name');
            $table->index('title');
            $table->index('horoscopeSignId');
            $table->index('horoscopeDate');
        });
    
        Schema::table('dailyhoroscopestatics', function (Blueprint $table) {
            $table->index('horoscopeSignId');
            $table->index('horoscopeDate');
        });
    
        Schema::table('defaultprofile', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('degree_or_diplomas', function (Blueprint $table) {
            $table->index('degreeName');
        });
    
        Schema::table('flaggroup', function (Blueprint $table) {
            $table->index('flagGroupName');
            $table->index('parentFlagGroupId');
        });
    
        Schema::table('fulltime_jobs', function (Blueprint $table) {
            $table->index('workName');
        });
    
        Schema::table('gifts', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('help_supports', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('highest_qualifications', function (Blueprint $table) {
            $table->index('qualificationName');
        });
    
        Schema::table('hororscope_signs', function (Blueprint $table) {
            $table->index('name');
            $table->index('displayOrder');
        });
    
        Schema::table('horoscope', function (Blueprint $table) {
            $table->index('horoscopeType');
            $table->index('horoscopeSignId');
        });
    
        Schema::table('horoscopes', function (Blueprint $table) {
            $table->index('zodiac');
        });
    
        Schema::table('intakeform', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('kundalis', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('kundali_matchings', function (Blueprint $table) {
            $table->index('boyName');
            $table->index('girlName');
        });
    
        Schema::table('languages', function (Blueprint $table) {
            $table->index('languageName');
        });
    
        Schema::table('livechat', function (Blueprint $table) {
            $table->index('userId');
            $table->index('partnerId');
            $table->index('chatId');
        });
    
        Schema::table('liveuser', function (Blueprint $table) {
            $table->index('userId');
            $table->index('fcmToken');
        });
    
        Schema::table('main_source_of_businesses', function (Blueprint $table) {
            $table->index('jobName');
        });
    
        Schema::table('marital_statuses', function (Blueprint $table) {
            $table->index('maritalStatus');
        });
    
        Schema::table('notifications', function (Blueprint $table) {
            $table->index('title');
        });
    
        Schema::table('payment', function (Blueprint $table) {
            $table->index('paymentMode');
            $table->index('paymentReference');
        });
    
        Schema::table('permissions', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('product_categories', function (Blueprint $table) {
            $table->index('name');
            $table->index('displayOrder');
        });
    
        Schema::table('rolepages', function (Blueprint $table) {
            $table->index('teamRoleId');
            $table->index('adminPageId');
        });
    
        Schema::table('roles', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('sessiontoken', function (Blueprint $table) {
            $table->index('token');
        });
    
        Schema::table('story_view_counts', function (Blueprint $table) {
            $table->index('userId');
            $table->index('storyId');
        });
    
        Schema::table('sub_category', function (Blueprint $table) {
            $table->index('parent_id');
        });
    
        Schema::table('systemflag', function (Blueprint $table) {
            $table->index('valueType');
            $table->index('name');
        });
    
        Schema::table('teammember', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('teamrole', function (Blueprint $table) {
            $table->index('name');
        });
    
        Schema::table('travel_countries', function (Blueprint $table) {
            $table->index('NoOfCountriesTravell');
        });
    
        Schema::table('users', function (Blueprint $table) {
            $table->index('name');
            $table->index('email');
        });
    
        Schema::table('user_chats', function (Blueprint $table) {
            $table->index('chatId');
        });
    
        Schema::table('user_device_details', function (Blueprint $table) {
            $table->index('appId');
            $table->index('deviceId');
            $table->index('fcmToken');
        });
    
        Schema::table('waitlist', function (Blueprint $table) {
            $table->index('userName');
        });
    
        Schema::table('wallettransaction', function (Blueprint $table) {
            $table->index('userId');
            $table->index('orderId');
        });
    
        Schema::table('withdrawmethods', function (Blueprint $table) {
            $table->index('method_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
