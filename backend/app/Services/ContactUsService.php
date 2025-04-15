<?php

namespace App\Services;

use App\Repositories\ContactUsRepository;
use Illuminate\Support\Str;

class ContactUsService
{
    protected $contactUsRepository;

    public function __construct(ContactUsRepository $contactUsRepository) {
        $this->contactUsRepository = $contactUsRepository;
    }

    public function createContactUs($data){
        $data['contact_us_id'] = Str::uuid();
        return $this->contactUsRepository->createContactUs($data);
    }

    public function getContactUsList(){
        return $this->contactUsRepository->getContactUsList();
    }
}