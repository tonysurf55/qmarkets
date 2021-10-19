$(document).ready(() => {
    $('body').append(
        `<div class="d-none modal show ajax-loader" style="background-color: rgba(255, 255, 255, 0.7);">
            <div class="justify-content-center modal-dialog modal-dialog-centered">
                <div class="spinner-load avatar-lg text-primary" role="status">
                    <img class="img-loading" src="/images/loading.gif" alt="">
                </div>
            </div>
        <div>`);
});

class Ajax
{
    /**
     * Submits an Ajax post request to the given url.
     *
     * @param url: The url to submit the request.
     * @param dataToSubmit: The data to submit.
     * @param skipError: Indicates whether to skip the automatic error for failed responses.
     * @param skipLoader: Do not sure loader while creating the ajax call.
     * @param params: Variables to pass with the response extra.
     *
     * @param loaderSection
     * @return Promise
     */
    async post(url, dataToSubmit, skipError = false, skipLoader = false, params = null, loaderSection = null) {
        if (!url) {
            throw "Ajax post invalid URL request";
        }

        return this.runPromise(skipError, skipLoader, params, loaderSection, {
                cache: false,
                type: 'post',
                url: url,
                data: dataToSubmit,
                headers: {
                    'X-Ajax-Header': 'post'
                },
                beforeSend: () => {
                    if (!skipLoader) {
                        this.showLoader(loaderSection)
                    }
                }
        });
    }

    /**
     * Submits an Ajax post request that contains multipart form data.
     *
     * @param url: The url to submit the request.
     * @param form: The data to submit.
     * @param skipError: Indicates whether to skip the automatic error for failed responses.
     * @param skipLoader: Do not sure loader while creating the ajax call.
     * @param params: Variables to pass with the response extra.
     *
     * @return Promise
     */
    async postForm(url, form, skipError = false, skipLoader = false, params = null) {
        if (!url) {
            throw "Ajax post invalid URL request";
        }

        let data = new FormData(form[0]);

        return this.runPromise(skipError, skipLoader, params, null, {
            cache: false,
            enctype: 'multipart/form-data',
            processData: false,
            contentType: false,
            type: 'post',
            url: url,
            data: data,
            headers: {
                'X-Ajax-Header': 'post'
            },
            beforeSend: () => {
                if (!skipLoader) {
                    this.showLoader()
                }
            }
        });
    }

    /**
     * Submits an Ajax post request to the given url with JSON data.
     *
     * @param url: The url to submit the request.
     * @param dataToSubmit: The data to submit.
     * @param csrfToken
     * @param skipError: Indicates whether to skip the automatic error for failed responses.
     * @param skipLoader: Do not sure loader while creating the ajax call.
     * @param params: Variables to pass with the response extra.
     *
     * @param loaderSection
     * @return Promise
     */
    async postAsBodyContent(url, dataToSubmit, csrfToken = null, skipError = false, skipLoader = false, params = null, loaderSection = null) {
        if (!url) {
            throw "Ajax post content invalid URL request";
        }

        return this.runPromise(skipError, skipLoader, params, loaderSection, {
            cache: false,
            type: 'post',
            url: url,
            data: JSON.stringify(dataToSubmit),
            headers: {
                'X-Ajax-Header': 'post',
                'csrf-token': csrfToken
            },
            beforeSend: () => {
                if (!skipLoader) {
                    this.showLoader(loaderSection)
                }
            }
        });
    }

    /**
     * Creates the form submit data.
     * @param params
     * @param options
     * @returns {FormData}
     */
    createPostParam(params, options = {}) {
        const dataToSubmit = new FormData();

        const isMultipart = options.headers
            && options.headers['Content-Type']
            && options.headers['Content-Type'] === 'multipart/form-data';

        Object.keys(params).forEach(key => {
            const keyName = key.replace(/^_/, '');

            if (typeof params[key] !== 'object' || isMultipart) {
                dataToSubmit.set(keyName, params[key]);
            }
            else {
                dataToSubmit.set(keyName, JSON.stringify(params[key]));
            }
        });

        return dataToSubmit;
    }

    /**
     * Runs the promise request.
     *
     * @param skipError
     * @param skipLoader
     * @param params
     * @param loaderSection
     * @param options
     *
     * @returns {Promise<unknown>}
     */
    runPromise(skipError = false, skipLoader = false, params = null, loaderSection = null, options) {
        return new Promise(async (resolve, reject) => {
            await $.ajax(options)
                .fail((response) => {
                    response = this.standardizeResponse(response);
                    console.error('failed', response);

                    if (!this.validateRedirection(response)) {
                        return;
                    }

                    if (skipError) {
                        reject(response, params);
                    } else {
                        let errorCode = 'errorCode' in response ? response.errorCode
                            : response.status ? response.status
                            : '';

                        let errorMessage = 'errorMessage' in response ? response.errorMessage
                            : response.responseText ? response.responseText
                            : '';

                        console.error(`${errorMessage} Error code: ${errorCode}`);
                    }

                    reject(response, params);
                })
                .done((response) => {
                    response = this.standardizeResponse(response);

                    if (!this.validateRedirection(response)) {
                        return;
                    }

                    if (!skipError && response.success === false) {
                        reject(response, params);
                    }

                    response['extra'] = params;
                    resolve(response);
                })
                .always(() => {
                    this.hideLoader(loaderSection);
                });
        });
    }

    /**
     * Gets a html response from the server.
     * If appendSection is field, it will automatically will append the retrieved data.
     *
     * @param url: The url to retrieve the data from.
     * @param dataToSubmit: The data to submit.
     * @param loaderSection: The area to show the loader, if empty not loader will be shown.
     * @param appendSection: The section to append the retrieved data.
     * @param clearBeforeAppend: Indicates whether to clear the section before appending the data.
     * @param params: Additional params that are passed back in the promise.
     * @returns {Promise<>}
     */
    async render(url, dataToSubmit, loaderSection, appendSection = false, clearBeforeAppend = false, params = null) {
        if (!url) {
            throw "Ajax render post invalid URL request";
        }

        return new Promise(async (resolve, reject) => {
            await $.ajax({
                async: true,
                cache: false,
                type: 'post',
                url: url,
                data: dataToSubmit,
                headers: {
                    'X-Ajax-Header': 'render'
                },
                beforeSend: (request) => {
                    if (loaderSection) {
                        this.showLoader(loaderSection)
                    }
                }
            })
                .fail((response) => {
                    response = response !== undefined && $.trim(response) !== ""
                        ? response
                        : $.i18n("Error occurred, please try again or call support");
                    reject(response, params);
                })
                .done((response) => {
                    if (appendSection) {
                        if (clearBeforeAppend) {
                            $(appendSection).html(response);
                        }
                        else {
                            $(appendSection).append(response);
                        }
                    }

                    resolve(response, params);
                })
                .always(() => {
                    if (loaderSection) {
                        this.hideLoader(loaderSection);
                    }
                });
        });
    }

    /**
     * Redirects to the response's result.
     *
     * @param {json} response: The server's response.
     * @returns {boolean}: True if the response is successfull, otherwise false.
     */
    validateRedirection(response) {
        if (response.logout === true) {
            window.location.href = `/`;
            return false;
        }

        if (response.redirect404 === true) {
            window.location.href = `/Error`;
            return false;
        }

        if (response.redirectToPatientSearch === true) {
            window.location.href = `/patient/search`;
            return false;
        }

        if (response.refresh === true) {
            window.location.href = window.location.href;
            return false;
        }

        return true;
    }

    /**
     * Gets the response data from the server.
     *
     * @param response: The server's response.
     *
     * @returns any: The response data object.
     */
    standardizeResponse(response) {
        if (Object.getPrototypeOf(response) !== Object.prototype) {
            if (!response || !Array.isArray(response)) {
                throw new Error('Invalid response from server.');
            }
            return response;
        }

        return "responseJSON" in response ? response.responseJSON : response;

    }

    /**
     * Creates the ajax process symbol.
     *
     * @param loaderSection: The name of the element to append the loader to it.
     */
    showLoader(loaderSection) {
        this.loading = true;

        if (!loaderSection || loaderSection === true) {
            $('body').addClass("ajax-loader-open");
            $('.ajax-loader').removeClass("d-none").addClass('d-block');
        }
        else {
            $(loaderSection).append(
                `<div class="spinner-grow spinner-grow-sm" role="status">
                    <span class="sr-only">Loading...</span>
                    </div>
                </div>`
            )
        }
    }

    /**
     * Hides the overlay;
     */
    hideLoader(loaderSection) {
        this.loading = false;

        if (!loaderSection || loaderSection === true) {
            $('body').removeClass("ajax-loader-open");
            $('.ajax-loader').addClass("d-none").removeClass('d-block');
        }
        else {
            $(loaderSection).find('.spinner-grow').remove();
        }
    }
}

const ajax = new Ajax();
