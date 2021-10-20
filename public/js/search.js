class Search
{
    constructor(tokens, urls) {
        this.tokens = tokens;
        this.urls = urls;
        this.form = 'form[name=search-form]';
        this.searchId = '#search-pattern';
        this.errorId = '#search-pattern-error';
        this.runIndexBtn = '#run-search-index';
        this.resetSearchBtn = '#reset-search';
        this.searchResultId = '#search-result';
        this.searchButtonId = '#search-button';

    }

    init() {
        // const options = {
        //     focusInvalid: false,
        //     errorPlacement: (($error, $element) => {
        //         $(this.errorId).append($error);
        //     }),
        //
        //     rules: {
        //         searchPattern: {
        //             required: true,
        //             minlength: 2
        //         }
        //     },
        //     messages: {
        //         searchPattern: {required: 'Please enter your search.'}
        //     },
        //     submitHandler: (form, event) => {
        //         event.preventDefault();
        //         return this.search();
        //     },
        // };

        /** Init the search form */
        // $(this.form).validate(options);
        /** Init the "Run search index" button */
        $(this.runIndexBtn).click(() => this.runIndex());

        const debounceOptions = {
            'leading': false,
            'trailing': true
        };
        $(this.searchId).keyup(_.debounce(this.search, 250, debounceOptions));
        // $(this.searchId).keyup(this.debounceSearch);

        $(this.resetSearchBtn).click(() => {
            $(this.searchId).val('');
            this.setHomeMode(true);
        })

        $(this.searchId).focus();

        $(this.searchButtonId).click(() => {
            const pattern = $(this.searchId).val();
            if (!pattern) {
                return;
            }

            if (pattern && pattern.length < 2) {
                $(this.errorId).removeClass('d-none');
                return;
            }

            this.search();
        });


        // $(this.listTableId).bootstrapTable({
        //     formatNoMatches: () => {
        //         return $.i18n('No data to show');
        //     },
        //     formatLoadingMessage: () => {
        //         return "";
        //     },
        //     onClickRow: (row, $element, field) => {
        //         if (field !== 'id') {
        //             this.loadEntity(row.id).then(r => {});
        //         }
        //     }
        // });
    }

    /**
     *
     * @param isHomeView
     */
    setHomeMode(isHomeView = false) {
       if (isHomeView) {
           $('.search-container').addClass('home-mode');
       } else {
           $('.search-container').removeClass('home-mode');
       }
    }

    /**
     *

     * @return {Promise<void>}
     */
    search = () => {
        $(this.errorId).addClass('d-none');

        let pattern = $(this.searchId).val();

        if (!pattern || pattern.length < 2) {
            $(this.searchResultId).html('');
            this.setHomeMode(true);
            return;
        }

        this.setHomeMode();
        const $loader = $('#search-loader');
        $loader.show();

        const data = {
            pattern: pattern.toLowerCase(),
            token: this.tokens.form
        };

        ajax.post(this.urls.search, data, false, true)
            .then(response => {
                const users = response.params.users ?? null;
                if (!users || users.length === 0) {
                    $(this.searchResultId).html('No result found.');
                    return;
                }

                let results = '';
                users.forEach((user) => {
                    results += '' +
                        '<div class="result-item">' +
                        `${user.full_name} ${user.mail} ${user.name} ${user.region} ${user.department}` +
                        '</div>';
                })

                $(this.searchResultId).html(results);
                console.log(response);
            })
            .finally(() => {
                $loader.hide();
            });
    }

    /**
     * Init the "Run search index" button
     */
    async runIndex() {
        const data = {
            token: this.tokens.form
        };

        await ajax.post(this.urls.runIndex, data)
            .then(response => {
                console.log(response);
            });
    }

}