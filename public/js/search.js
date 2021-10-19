class Search
{
    constructor(tokens, urls) {
        this.tokens = tokens;
        this.urls = urls;
        this.form = 'form[name=search-form]';
        this.searchId = '#search-pattern';
        this.errorId = '#search-pattern-error';
        this.runIndexBtn = '#run-search-index';
    }

    init() {
        const options = {
            focusInvalid: false,
            errorPlacement: (($error, $element) => {
                $(this.errorId).append($error);
            }),

            rules: {
                searchPattern: {required: true}
            },
            messages: {
                searchPattern: {required: 'Please enter your search.'}
            },
            submitHandler: (form, event) => {
                return this.search(form, event);
            },
        };

        /** Init the search form */
        $(this.form).validate(options);
        /** Init the "Run search index" button */
        $(this.runIndexBtn).click(() => this.runIndex());
    }

    /**
     *
     * @param form
     * @param event
     * @return {Promise<void>}
     */
    async search(form, event) {
        event.preventDefault();

        const data = {
            pattern: $(this.searchId).val(),
            token: this.tokens.form
        };

        await ajax.post(form.action, data)
            .then(response => {
                console.log(response);
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