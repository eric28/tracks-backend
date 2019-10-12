import {AppConfig} from "../services/AppConfig";

export class HttpHelper {

    static get(url, queryParams = []) {

        let urlWithParams = new URL(this.generateUrl(url));
        urlWithParams.search = new URLSearchParams(queryParams);

        return fetch(urlWithParams, {
            method: "GET",
            headers: this.makeHeaders(),
            credentials: "same-origin"
        }).then(this.handleErrors);
    }

    static post(url, params = []) {

        return fetch(this.generateUrl(url), {
            method: "POST",
            headers: this.makeHeaders(),
            credentials: "same-origin",
            body: JSON.stringify(params)
        })
            .then(this.handleErrors);
    }

    static put(url, params = []) {

        return fetch(this.generateUrl(url), {
            method: "PUT",
            headers: this.makeHeaders(),
            credentials: "same-origin",
            body: JSON.stringify(params)
        })
            .then(this.handleErrors);
    }

    static handleErrors(response) {
        if (!response.ok) {
            return response.json().then(json => {
                return Promise.reject(json);
            })
        }
        return response.json();
    }

    static makeHeaders() {
        let token = document.head.querySelector('meta[name="csrf-token"]');

        if (!token) console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');

        // noinspection JSUnresolvedVariable
        const headersValues = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token.content
        };

        let headers = new Headers();

        Object.keys(headersValues).forEach(key => {
            headers.append(key, headersValues[key]);
        });

        return headers;
    }

    static generateUrl(url) {
        let bar = url.length > 0 && url.substr(0) !== "/" ? "/" : "";

        return AppConfig.URL + "/api/v1" + bar + url;
    }
}