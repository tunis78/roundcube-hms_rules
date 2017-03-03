/**
 * hms_rules plugin script
 *
 * @licstart  The following is the entire license notice for the
 * JavaScript code in this file.
 *
 * Copyright (c) 2017, Andreas Tunberg <andreas@tunberg.com>
 *
 * The JavaScript code in this page is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * @licend  The above is the entire license notice
 * for the JavaScript code in this file.
 */

window.rcmail && rcmail.addEventListener('init', function(evt) {

    //Add class to disabled rules
    if (rcmail.env.disabled_rules) {
        var label_disabled = rcmail.get_label('hms_rules.disabled');
        $.each(rcmail.env.disabled_rules, function(k, v) {$('#rcmrow' + v).addClass('disabled').children('td').append(' - ' + label_disabled);});
    }

    if (rcmail.env.rules_reload) {
        window.top.location.href = rcmail.env.comm_path + '&_action=plugin.rules&_rid=' + rcmail.env.rules_reload
    }

    rcmail.register_command('plugin.hmsrules-add', function() { rcmail.hmsrules_add() },true);
    rcmail.register_command('plugin.hmsrules-delete', function() { rcmail.hmsrules_del() });
    rcmail.register_command('plugin.hmsrules-moveup', function() { rcmail.hmsrules_moveup() });
    rcmail.register_command('plugin.hmsrules-movedown', function() { rcmail.hmsrules_movedown() });
    
    rcmail.addEventListener('plugin.rules-reload', function(id) { 
        var rid = (id > 0) ? '&_rid=' + id : '';
        location.href = './?_task=settings&_action=plugin.rules' + rid;
    } );

    if (rcmail.gui_objects.rulelist) {
        rcmail.rules_list = new rcube_list_widget(rcmail.gui_objects.rulelist,
          {multiselect:false, draggable:false, keyboard:true});

        rcmail.rules_list
          .addEventListener('select', function(e) { rcmail.hmsrules_select(e); })
          .init();

        if (rcmail.env.rules_selected) {
            rcmail.rules_list.select(rcmail.env.rules_selected);
        }
    }

    if (rcmail.gui_objects.ruleform) {  
        rcmail.register_command('plugin.hmsrule-submit', function() {
            rcmail.set_busy(true, 'loading');
            rcmail.gui_objects.ruleform.submit();
        }, true);

        rcmail.register_command('plugin.hmsrule-abort', function(prop) {
            if (prop != null) {
                rcmail.set_busy(true, 'loading');
                location.href = './?_task=settings&_action=plugin.rules-edit&_rid=' + prop;
            }
        }, true);

        rcmail.register_command('plugin.hmsrule-criteria-edit', function(prop) {
            if (prop != null) {
                id = prop.split(':');
                rcmail.set_busy(true, 'loading');
                location.href = './?_task=settings&_action=plugin.rules-criteria&_rid=' + id[0] + '&_cid=' + id[1];
            }            
        }, true);
        rcmail.register_command('plugin.hmsrule-criteria-delete', function(prop) {
            if (prop != null) {
                id = prop.split(':');
                rcmail.set_busy(true, 'loading');
                location.href = './?_task=settings&_action=plugin.rules-edit&_act=criteriadelete&_rid=' + id[0] + '&_cid=' + id[1];
            }
        }, true);
        rcmail.register_command('plugin.hmsrule-action-edit', function(prop) {
            if (prop != null) {
                id = prop.split(':');
                rcmail.set_busy(true, 'loading');
                location.href = './?_task=settings&_action=plugin.rules-action&_rid=' + id[0] + '&_aid=' + id[1];
            }
            
        }, true);
        rcmail.register_command('plugin.hmsrule-action-moveup', function(prop) {
            if (prop != null) {
                id = prop.split(':');
                rcmail.set_busy(true, 'loading');
                location.href = './?_task=settings&_action=plugin.rules-edit&_act=actionup&_rid=' + id[0] + '&_aid=' + id[1];
            }
        }, true);
        rcmail.register_command('plugin.hmsrule-action-movedown', function(prop) {
            if (prop != null) {
                id = prop.split(':');
                rcmail.set_busy(true, 'loading');
                location.href = './?_task=settings&_action=plugin.rules-edit&_act=actiondown&_rid=' + id[0] + '&_aid=' + id[1];
            }
        }, true);
        rcmail.register_command('plugin.hmsrule-action-delete', function(prop) {
            if (prop != null) {
                id = prop.split(':');
                rcmail.set_busy(true, 'loading');
                location.href = './?_task=settings&_action=plugin.rules-edit&_act=actiondelete&_rid=' + id[0] + '&_aid=' + id[1];
            }
        }, true);
    }

    $('input:not(:hidden):first').focus();

});

// Rules selection
rcube_webmail.prototype.hmsrules_select = function(list)
{
    var id = list.get_single_selection();

    if (id != null) {
        //this.load_hmsruleframe(list.rows[id].uid);
        this.load_hmsruleframe(id);
        this.enable_command('plugin.hmsrules-delete', true);
        this.enable_command('plugin.hmsrules-moveup', (list.get_first_row() != id));
        this.enable_command('plugin.hmsrules-movedown', (list.get_last_row() != id));
    }
};

// button actions
rcube_webmail.prototype.hmsrules_add = function()
{
    this.load_hmsruleframe();
    this.rules_list.clear_selection();
    this.enable_command('plugin.hmsrules-delete','plugin.hmsrules-moveup','plugin.hmsrules-movedown', false);
};

rcube_webmail.prototype.hmsrules_del = function()
{
    var id = this.rules_list.get_single_selection();
    if (id != null && confirm(this.get_label('hms_rules.ruledeleteconfirm'))) {
        this.set_busy(true);
        this.http_post('plugin.rules-actions', '_act=delete&_rid=' + id);
        this.set_busy(false);
        //location.href = './?_task=settings&_action=plugin.rules&_act=delete&_rid=' + id;
    }
};

rcube_webmail.prototype.hmsrules_moveup = function()
{
    var id = this.rules_list.get_single_selection();
    if (id != null) {
        this.set_busy(true);
        this.http_post('plugin.rules-actions', '_act=moveup&_rid=' + id);
        this.set_busy(false);
        //location.href = './?_task=settings&_action=plugin.rules&_act=moveup&_rid=' + id;
    }
};

rcube_webmail.prototype.hmsrules_movedown = function()
{
    var id = this.rules_list.get_single_selection();
    if (id != null) {
        this.set_busy(true);
        this.http_post('plugin.rules-actions', '_act=movedown&_rid=' + id);
        this.set_busy(false);
        //location.href = './?_task=settings&_action=plugin.rules&_act=movedown&_rid=' + id;
    }
};

// load rule frame
rcube_webmail.prototype.load_hmsruleframe = function(id)
{
    var has_id = typeof(id) != 'undefined' && id != null;
    if (this.env.contentframe && window.frames && window.frames[this.env.contentframe]) {
        target = window.frames[this.env.contentframe];
        var lock = '';//this.set_busy(true, 'loading');
        target.location.href = this.env.comm_path + '&_action=plugin.rules-edit'
          + (has_id ? '&_rid='+id : '') + '&_unlock=' + lock;
    }
};