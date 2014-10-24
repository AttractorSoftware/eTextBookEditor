var EditPermissions = {
    modal: $('#permissionFormModal')
    ,emailInput: $('#emailInput')
    ,addBtn: $('.add-permission')
    ,saveBtn: $('#savePermissions')
    ,saveAction: $('#permissionFormModal').attr('action')
    ,listDisplay: $('.permission-user-list')
    ,userList: []
    ,init: function() {
        var $this = this;
        this.addPermissionsActivate();
        this.saveBtn.bind('click', function() {
            $this.save();
        });
        this.parseCurrentUsers();
    }
    ,parseCurrentUsers: function() {
        var currentUsers = this.listDisplay.find('.current-users .user');
        for(var i = 0; i < currentUsers.length; i++) {
            this.userList.push($(currentUsers[i]).html());
        } this.renderUserList();
    }
    ,addPermissionsActivate: function() {
        var $this = this;
        this.addBtn.bind('click', function() { console.debug('q'); $this.addInputUser(); });
        this.emailInput.bind('keyup', function(e){
            if(e.which == 13) {
                $this.addInputUser();
            }
        });
    }
    ,addInputUser: function() {
        if(this.emailInput.val().length > 5) {
            this.userList.push(this.emailInput.val());
            this.emailInput.val('');
            this.renderUserList();
        }
    }
    ,renderUserList: function() {
        this.listDisplay.html('');
        for(var i = 0; i < this.userList.length; i++) {
            this.listDisplay.append(this.permissionUserWrap(this.userList[i]));
        }
    }
    ,permissionUserWrap: function(user) {
        var $this = this;
        var wrap = $(
            '<span class="permission-user">' +
                user + '&nbsp;<span class="glyphicon glyphicon-remove remove"></span>' +
            '</span>'
        );
        wrap.find('.remove').bind('click', function(){
            $this.removeFromUserList(user);
            $this.renderUserList();
        });
        return wrap;
    }
    ,removeFromUserList: function(user) {
        var newList = [];
        for(var i = 0; i < this.userList.length; i++) {
            if(this.userList[i] != user) {
                newList.push(this.userList[i]);
            }
        } this.userList = newList;
        return true;
    }
    ,save: function() {
        $.post(this.saveAction, { users: this.userList }, function(response) {
            $('#permissionFormModal').modal('hide');
        })
    }
}

$(function(){
   EditPermissions.init();
});