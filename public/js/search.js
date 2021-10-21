/**
 * Search page
 */
class Search
{
    /**
     * Creates an instance of the class.
     *
     * @param tokens
     * @param urls
     */
    constructor(tokens, urls) {
        this.urls = urls;
        this.tokens = tokens;
        this.searchId = '#search-pattern';
        this.form = 'form[name=search-form]';
        this.resetSearchBtn = '#reset-search';
        this.runIndexBtn = '#run-search-index';
        this.searchButtonId = '#search-button';
        this.searchResultId = '#search-result';
        this.resetFilterButton = '.reset-filter';
        this.searchNoResultId = '#search-no-result';
        this.errorSearchEmpty = '#search-empty-error';
        this.errorSearchMinLength = '#search-min-error';
        this.searchResultTableId = '#search-results-table';
        this.filterSelector = '.region-filter, .department-filter';
    }

    /**
     * Init the elements on the page after the DOM is loaded.
     */
    init() {
        /** Init the "Run search index" button */
        $(this.runIndexBtn).click(() => this.runIndex());
        this.initSearchField();
        this.initSearchButton();
        this.initFilters();
    }

    /**
     * Init the search filed
     */
    initSearchField() {
        const debounceOptions = {
            'leading': false,
            'trailing': true
        };

        $(this.searchId).keyup(_.debounce(this.search, 250, debounceOptions));
        $(this.searchId).focus();

        $(this.resetSearchBtn).click(() => {
            $(this.searchId).val('');
            this.setHomeMode(true);
        })
    }

    /**
     * Init the search button
     */
    initSearchButton() {
        $(this.searchButtonId).click(() => {
            const pattern = $(this.searchId).val();
            if (!pattern) {
                $(this.errorSearchEmpty).removeClass('d-none');
                return;
            }
            
            if (pattern.length < 2) {
                $(this.errorSearchMinLength).removeClass('d-none');
                return;
            }

            this.search();
        });
    }

    /**
     * Init the region and department filter
     */
    initFilters() {
        // Add filter
        $(this.filterSelector).change(this.search);

        // Reset filter
        $(this.resetFilterButton).click((event) => {
            const $button = $(`#${event.target.id}`);
            const type = $button.attr('data-type');
            $(`.${type}-filter`).prop("checked", false);
            this.search();
        });
    }

    /**
     * Switch the screen view
     *
     * @param isHomeView: when true, display the fields on the center of the screen
     */
    setHomeMode(isHomeView = false) {
       if (isHomeView) {
           $('.search-container').addClass('home-mode');
           $(this.searchResultId).addClass('d-none');
       } else {
           $('.search-container').removeClass('home-mode');
       }

        // Hide the errors and the "no result" message
        $(this.errorSearchEmpty).addClass('d-none');
        $(this.errorSearchMinLength).addClass('d-none');
        $(this.searchNoResultId).addClass('d-none');
    }

    /**
     * Search for a pattern
     *
     * @return {Promise<void>}
     */
    search = () => {
        let pattern = $(this.searchId).val();

        if (!pattern || pattern.length < 2) {
            this.setHomeMode(true);
            return;
        }

        this.setHomeMode();
        const $loader = $('#search-loader');
        $loader.show();

        const data = {
            pattern: pattern.toLowerCase(),
            token: this.tokens.form,
            ...this.getFilters()
        };

        ajax.post(this.urls.search, data, false, true)
            .then(response => {
                this.showSearchResults(response.params.users ?? null);
            })
            .finally(() => { $loader.hide(); });
    }

    /**
     * Display the search results
     * @param results
     */
    showSearchResults(results) {
        if (!results || results.length === 0) {
            $(this.searchNoResultId).removeClass('d-none');
            $(this.searchResultId).addClass('d-none');
            return;
        }

        $(this.searchResultTableId).bootstrapTable('removeAll')
        $(this.searchResultTableId).bootstrapTable('append', results);
        $(this.searchResultId).removeClass('d-none');
    }

    /**
     * Get the selected regions and departments
     *
     * @return {{region: *[], department: *[]}}
     */
    getFilters() {
        const filters = { region: [], department: [] };

        $(this.filterSelector).each((index, element) => {
            const $filter = $(element);
            if (!$filter.is(':checked')) {
                return;
            }

            const id = $filter.attr('data-id');
            const type = $filter.attr('data-type');
            filters[type].push(id);
        });

        return filters;
    }

    /**
     * Init the "Run search index" button
     * Run the stored procedure which create the keywords and scores tables.
     */
    async runIndex() {
        await ajax.post(this.urls.runIndex, { token: this.tokens.form })
            .then(() => {
                // Reset the page display
                $(this.searchId).val('');
                this.setHomeMode(true);

                // Close the filters
                $('#regions-box-content').collapse('hide');
                $('#departments-box-content').collapse('hide');
            });
    }
}