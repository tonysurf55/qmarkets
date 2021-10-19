class Search
{
    constructor(tokens, options = {}) {
        this.tokens = tokens;
        this.form = 'form[name=search-form]';
        this.searchId = '#search-pattern';
    }

    init() {
        const options = {
            rules: {
                searchPattern: { required: true }
            },
            messages: {},
            submitHandler: (form, event) => {
                return this.search(form, event);
            },
        };

        $(this.form).validate(options);
    }

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
}