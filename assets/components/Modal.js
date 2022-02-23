import { Modal as BsModal } from 'bootstrap';

class Modal
{
    constructor(container)
    {
        this.$container = undefined === container ? $('body') : container;
        this.$modal = $('[data-the-modal]');
        this.$modalContent = this.$modal.find('[data-modal-content]');
        this.bind();
    }

    bind()
    {
        var that = this;

        this.$container.find('[data-add-modal]').on('click', function (e) {
            e.preventDefault();
            that.fire($(this).attr('href'));
        });
    }

    fire(url, method, data)
    {
        var that = this;

        method = undefined === method ? 'get' : method;
        data = undefined === data ? {} : data;

        that.modal = new BsModal(that.$modal);
        that.modal.toggle();

        $.ajax({
            url: url,
            dataType: 'html',
            data: data,
            type: method
        })
            .done(function (response) {
                that.parseResponse(response);
            })
            .fail(function (event) {
                if (401 === event.status || 403 === event.status) {
                    window.location.reload();
                    return;
                }

                that.modal.toggle();
            });
    }

    parseResponse(response)
    {
        var that = this,
            $response = $(response),
            $form = $response.find('[data-modal-response-form], [data-modal-response-form-prevalid]'),
            $formClassic = $response.find('[data-modal-response-form-classic]'),
            $toHide = $response.find('[data-modal-response-tohide]'),
            $close = $response.find('[data-modal-response-close]'),
            $valid = $response.find('[data-modal-response-valid]');

        $close.on('click', function(e) {
            e.preventDefault();
            that.modal.toggle();
        });

        $response.find('[data-simple-select2]').each(function () {
            $(this).select2({ dropdownParent: that.$modalContent });
        });

        if (0 < $form.length) {
            var callback = $form.data('modal-response-callback');

            $valid.on('click', function(e){
                e.preventDefault();
                $form.trigger('submit');
            });

            $form.on('submit', function(e) {
                e.preventDefault();

                let prevalid = false,
                    formDatas = $(this).serialize();

                if (undefined !== $(this).data('modal-response-form-prevalid')) {
                    formDatas+= '&prevalid=1';
                    prevalid = true;
                }

                $.ajax($form.attr('action'),
                {
                    data: formDatas,
                    type: 'post',
                })
                    .done(function (response, state, xhr) {
                        var contentType = xhr.getResponseHeader('content-type');

                        if (contentType.indexOf('json') > -1) {
                            that.closeAfterValidation(callback, response);
                            return;
                        }

                        var $response = $(response),
                            hasError = 0 < $response.find('.error_list').length;

                        if (true === prevalid && false === hasError) {
                            $form.off('submit');
                            $form.trigger('submit');
                        }

                        if (false === hasError) {
                            that.closeAfterValidation(callback, response);
                        }

                        that.parseResponse(response);
                    })
                    .fail(function () {
                        that.modal.toggle();
                    });
            });
        } else if (0 < $formClassic.length) {
            $formClassic.one('submit', function (e) {
                e.preventDefault();
                $formClassic.trigger('submit');
            });
        }

        $toHide.remove();

        this.$modalContent.html($response);
    }

    closeAfterValidation(callback, response)
    {
        if ('reload' === callback) {
            location.reload();
        }

        this.modal.toggle();
    }
}

export { Modal };
