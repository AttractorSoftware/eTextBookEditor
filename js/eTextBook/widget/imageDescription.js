var eTextBookWidgetImageDescription = eTextBookWidget.extend({
    defaults: {
        slug: "image-description"
        ,title: "Описание картинки"
        ,templateName: 'imageDescription'
    }

    ,finishEdit: function() {
        for(var i = 0; i < this.editCont.find('image-title').length; i++) {
            var title = $(this.editCont.find('image-title')[i]);
            title.find('view-element').html(title.find('edit-element input').val());
        }
    }

    ,startEdit: function() {
        for(var i = 0; i < this.editCont.find('image-title').length; i++) {
            var title = $(this.editCont.find('image-title')[i]);
            title.find('edit-element input').val(title.find('view-element').html());
        }
    }

    ,activate: function() {
        var $this = this;
        this.editCont.find('image-description images').append('<edit-element class="add-image"><span class="glyphicon glyphicon-picture"></span></edit-element>');
        this.editCont.find('image-description images item image-view').append('<edit-element class="glyphicon glyphicon-remove remove"></edit-element>');
        this.editCont.find('image-description images item image-title').append('<edit-element><input type="text" /></edit-element>');
        this.editCont.find('descs').html('');
        this.editCont.find('image-view span').remove();
        this.bindRemoveEvent();
        this.editCont.find('.add-image').bind('click', function() {
            App.fileManager.pickFile(function(path){
                $this.editCont.find('.add-image').before(
                    '<item>' +
                        '<image-view style="background-image: url(' + path + ')"></image-view>' +
                        '<image-title>' +
                            '<view-element>Title</view-element>' +
                            '<edit-element><input type="text" /></edit-element>' +
                        '</image-title>' +
                    '</item>'
                );
                $this.bindRemoveEvent();
            });
        });
    }

    ,bindRemoveEvent: function() {
        this.editCont.find('images item .remove').unbind('click');
        this.editCont.find('images item .remove').bind('click', function() {
            $(this).parent().parent().remove();
        });
    }

    ,addImage: function(path) {

    }

    ,viewActivate: function() {

        this.contentContainer.find('descs').html('');
        this.contentContainer.find('image-view span').remove();

        this.descs = [];

        var images = this.contentContainer.find('images item');

        for(var i = 0; i < images.length; i++) {
            var image = $(images[i]);
            var pos = i + 1;
            image.find('image-view').append('<span>' + pos + '</span>');
            this.descs.push({
                answer: pos
                ,text: image.find('image-title view-element').html()
            });
        }

        this.descs = this.descs.sort(function() { return Math.random() > 0.5 ? 1 : -1; });

        for(var i = 0; i < this.descs.length; i++) {
            var desc = this.descs[i];
            var select = this.generateSelect(this.descs.length, desc.answer );
            var item = $('<item>' + desc.text + '</item>');
            item.prepend(select);
            this.contentContainer.find('descs').append(item);
        }

        this.contentContainer.find('select').bind('change', function() {
            if($(this).attr('answer') == $(this).val()) {
                $(this).parent().addClass('success');
                $(this).parent().removeClass('failed');
            } else {
                $(this).parent().addClass('failed');
                $(this).parent().removeClass('success');
            }
        });

    }

    ,generateSelect: function(count, answer) {
        var select = $('<select answer="' + answer + '"><option></option></select>');

        for(var i = 0; i < count; i++) {
            var pos = i + 1;
            select.append('<option value="' + pos + '">' + pos + '</option>');
        } return select;
    }
});

App.eTextBookWidgetRepository.registerWidget(eTextBookWidgetImageDescription);