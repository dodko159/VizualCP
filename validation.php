<?php

class ValidationMessage implements JsonSerializable
{
    public $key;
    public $args = array();

    public function __construct($key, $args)
    {
        $this->key = $key;
        $this->args = $args;
    }

    public function jsonSerialize(): array
    {
        return [
            'key' => $this->key,
            'args' => $this->args
        ];
    }
}

function validate(ApiRequest $apiRequest): array
{
    return array_merge(
        validateContactEmail($apiRequest->priceOffer->contact)
    );
}

function validateContactEmail(ContactRequest $contact): array
{
    if (!$contact->email) {
        return ["contact-email" => [new ValidationMessage("required", [])]];
    }

    if (!filter_var($contact->email, FILTER_VALIDATE_EMAIL)) {
        return ["contact-email" => [new ValidationMessage("invalidEmailFormat", [])]];
    }

    return [];
}
?>