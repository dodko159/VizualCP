import {ApiRequest} from "./req/ApiRequest.js";
import {AppConfigResponse} from "./res/AppConfigResponse.js";

const API_URL = 'api.php';
const API_URL_SUBMISSION = 'api-submission.php';
const API_URL_CONFIG = 'api-app-config.php';

export async function getAppConfig(): Promise<AppConfigResponse> {
    return fetch(API_URL_CONFIG).then(response => response.json())
}

export function getForm(): Promise<any> {
    return fetch(API_URL + '?getApiResponse=true')
}

export function updateForm(req: ApiRequest): Promise<any> {
    return fetch(API_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(req)
    })
}

export function submitForm(recaptchaToken: string | undefined): Promise<any> {
    return fetch(API_URL_SUBMISSION, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            'g-recaptcha-response': recaptchaToken
        })
    })
}