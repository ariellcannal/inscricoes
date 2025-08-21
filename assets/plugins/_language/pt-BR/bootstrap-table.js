BootstrapTable.LOCALES['pt-BR'] = BootstrapTable.LOCALES['pt'] = {
        formatLoadingMessage: function () {
            return 'Carregando, aguarde......';
        },
        formatRecordsPerPage: function (pageNumber) {
            return sprintf('%s linhas por página', pageNumber);
        },
        formatShowingRows: function (pageFrom, pageTo, totalRows) {
            return sprintf('Mostrando de %s a %s de %s linhas', pageFrom, pageTo, totalRows);
        },
        formatDetailPagination: function (totalRows) {
            return sprintf('Mostrando %s linhas', totalRows);
        },
        formatSearch: function () {
            return 'Buscar';
        },
        formatNoMatches: function () {
            return 'Não encontramos registros';
        },
        formatPaginationSwitch: function () {
            return 'Mostrar/esconder pagnação';
        },
        formatRefresh: function () {
            return 'Atualizar';
        },
        formatToggle: function () {
            return 'Toggle';
        },
        formatColumns: function () {
            return 'Colunas';
        },
        formatAllRows: function () {
            return 'Todos';
        }
    };

    $.extend(BootstrapTable.DEFAULTS, BootstrapTable.LOCALES['pt-BR']);