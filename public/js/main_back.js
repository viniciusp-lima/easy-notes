jQuery(document).ready(function($) {

    $('.js-create-folder').click(function() {
        const element = $(this);

        swal({
            text: 'Criar nova pasta',
            content: "input",
            button: {
              text: "Criar",
              closeModal: false,
            },
        })
        .then(folderName => {
            if(folderName) {
                element.data('params', `folder[name]:${folderName}`);
                $.processParams(element);
                swal.close();
            }
        });
    });
    
    // Manipula envios de formulários
    $(document).on('submit', '.js-action-submit', function(event) {
        event.preventDefault();
        const element = $(this);

        const button = element.find('.btn:not(.btn-secondary)');
        button.addClass('loading');
        button.prop('disabled', true);

        const form = new FormData(event.target);
		const data = Object.fromEntries(form.entries());

        $.processDataSending(element, data);
    });

    // Manipula cliques em elementos
    $(document).on('click', '.js-action-click', function(event) {
        event.preventDefault();
        event.stopPropagation();
        const element = $(this);

        if(element.hasClass('btn')) {
            element.addClass('loading');
        }

        $.processParams(element);
    });

    // Manipula ações de deletar
    $(document).on('click', '.js-action-delete', function(event) {
        event.preventDefault();
        event.stopPropagation();
        const element = $(this);

        const browserLanguage = $.identifyBrowserLanguage() == 'en';
        const title = browserLanguage ? 'Delete item' : 'Deletar item';
        const text = browserLanguage ? 'This action cannot be undone' : 'Essa ação não pode ser desfeita';

        const buttonCancel = browserLanguage ? 'Cancel' : 'Cancelar';
        const buttonDelete = browserLanguage ? 'Delete' : 'Excluir';

        swal({
            title: element.data('title') ?? title,
            text: element.data('text') ?? text,
            icon: "warning",
            buttons: [buttonCancel, buttonDelete],
            dangerMode: true,
        })
        .then(willDelete => {
            if(willDelete) {
                $.processParams(element);
            }
        });
    });

    // Função para enviar dados via AJAX
    $.processDataSending = function(element, params, obj=false, processed=false) {
        const elAttr = element.data('action').split(/:|__/);
    
        if(!obj) {
            obj = {
                category: elAttr.shift(), // Remove e obtém o primeiro elemento
                file: elAttr[0],
                method: elAttr[1]
            };
        }
    
        const data = {
            action: 'ajax_action',
            category: obj.category,
            file: obj.file,
            action_type: obj.method,
            data: params
        }
    
        if(element.data('lightbox')) data.lightbox = element.data('lightbox');
    
        $.ajax({
            method: 'POST',
            url: ajax_object.ajax_url,
            data: data,
        }).done(result => {
            if(element.hasClass('loading')) {
                element.removeClass('loading')
            } else {
                element.find('.loading').removeClass('loading');
            }

            if(element.hasClass('js-action-submit')) {
                const button = element.find('.btn:not(.btn-secondary)');
                button.prop('disabled', false);
            }
            
            $.showAlert(result.message);
            $.updateTarget(element, result);
            $.showLightbox(element, result.html);
            $.manageNavigation(element, result);
            $.processDataOnSuccess(element, params, processed);

            if (result.success && !processed) {
                return;
            }
        });
    }

    // Função chamada em caso de sucesso no processamento de dados
    $.processDataOnSuccess = function(element, params, processed=false) {
        const elAttr = element.data('action-success');
    
        if (elAttr && !processed) {
            const attr = elAttr.split(/:|__/);
    
            const obj = {
                category: attr.shift(),
                file: attr[0],
                method: attr[1]
            };
    
            $.processDataSending(element, params, obj, true);
        }
    }

    $.processParams = function(element) {
        let data = {};
        let parts = '';

        if (element.data('params')) {
            parts = element.data('params').split('&');

            $(parts).each((index, part) => {
                const [key, value] = part.split(':');
                data[key] = value;
            });
        }

        $.processDataSending(element, data);
    }
    
    // Função para atualizar o alvo com HTML
    $.updateTarget = function(element, result) {
        const elAttr = element.data('update-target');
        
        if(elAttr && result.html) {
            $(elAttr).html(result.html);
        } else {
            if(typeof result != 'object' && result.length != 0) {
                const tempDiv = $('<div>').html(result);
                const mainContent = tempDiv.find('.content').html();
                $(elAttr).html(mainContent);
            }
        }
    }

    // Função para exibir alertas
    $.showAlert = function(message) {
        if(message) {

            const alertEl = $('.message');
            if(alertEl.length) {
                alertEl.html(message);
    
                setTimeout(() => {
                    try {
                        const alert = bootstrap.Alert.getOrCreateInstance('.alert');
                        alert.close();
                    } catch(error) {
                        return false
                    }
                }, 5000);
            }

            const toastEl = $('.toast-wrapper');
            if(toastEl.length) {
                toastEl.html(message);
                $('.toast').toast('show');
            }
        }
    }

    // Função para gerenciar navegação
    $.manageNavigation = function(element, data) {
        if(data.success) {
            if(element.data('reload')) {
                window.location.reload();
            }

            if(element.data('redirect-url')) {
                window.location = element.data('redirect-url');
            }
            
            if(data.pathName) {
                window.location.pathname = data.pathName;
            }
        }
    }

    // Função para exibir lightbox (modal, offcanvas)
    $.showLightbox = function(element, html) {
        const elAttr = element.data('lightbox');
        if(elAttr) {
            const comp_type = elAttr === 'modal' ? elAttr : 'offcanvas';
            $('body').append(html);

            if(comp_type === 'modal') {
                $(`.${comp_type}`).modal('show');
            } else {
                $(`.${comp_type}`).offcanvas('show');
            }

            $(`.${comp_type}`).on(`hidden.bs.${comp_type}`, function () {
                $(this).remove();
            });
        }
    }

    $.identifyBrowserLanguage = function() {
        let browserLanguage = navigator.language || navigator.userLanguage;
        browserLanguage = browserLanguage.toLowerCase();

        if (browserLanguage.indexOf('en') !== -1) {
            return 'en';
        } else if (browserLanguage.indexOf('pt') !== -1) {
            return 'pt'
        } else {
            return 'en';
        }
    }

});