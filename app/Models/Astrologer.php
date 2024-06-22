<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Astrologer extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = 'astrologers';
    protected $fillable = [
        'userId',
        'name',
        'email',
        'contactNo',
        'gender',
        'birthDate',
        'primarySkill',
        'allSkill',
        'languageKnown',
        'profileImage',
        'charge',
        'experienceInYears',
        'dailyContribution',
        'hearAboutAstroguru',
        'isWorkingOnAnotherPlatform',
        'whyOnBoard',
        'interviewSuitableTime',
        'currentCity',
        'mainSourceOfBusiness',
        'highestQualification',
        'degree',
        'college',
        'learnAstrology',
        'astrologerCategoryId',
        'instaProfileLink',
        'facebookProfileLink',
        'linkedInProfileLink',
        'youtubeChannelLink',
        'websiteProfileLink',
        'isAnyBodyRefer',
        'minimumEarning',
        'maximumEarning',
        'loginBio',
        'NoofforeignCountriesTravel',
        'currentlyworkingfulltimejob',
        'goodQuality',
        'biggestChallenge',
        'whatwillDo',
        'isVerified',
    ];


}
