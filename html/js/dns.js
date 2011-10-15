
function getRecordTemplateCopy(type)
{
    var copy = $('table.add-template', $('#dns-' + type)).clone().removeClass('add-template').addClass('template-generated');
    try { copy = templateRecordReplacements(copy, type); } catch (e) { alert(e); }
    if (type ==  'mx') $('select', $(copy)).change(switchMailProvider);
    return copy;
}

function getRowTemplateCopy(table)
{
    var copy = $('tr.add-template', table).clone().removeClass('add-template').addClass('template-generated');
    try { copy = templateRowReplacements(table, copy); } catch (e) { alert(e); }
    return copy
}

function templateRecordReplacements(copy, type)
{
    var idx = $('#dns-' + type).find('table.record').length - 1;
    var html = copy.html()

    html = html.split('$$').join(idx);
    //html = html.split('$#').join('0');
    copy.attr('id', copy.attr('id').split('$$').join(idx));
    copy.html(html);
    copy.find('tr.record').each(function(i)
    {
        if (!$(this).hasClass('add-template')) $(this).html($(this).html().split('$#').join('0'));
    });
    return copy;
}

function templateRowReplacements(table, copy)
{
    var html = copy.html()
    var idx = $(table).find('tr.record').length - 1;
    html = html.split('$#').join(idx);
    copy.html(html);
    return copy;
}

function addRecordTemplate(type)
{
    var copy = getRecordTemplateCopy(type);
    copy.insertBefore($('table.add-template', $('#dns-' + type))).find('input:first').focus();
    //$('html, body').animate({ scrollTop: $(copy).offset().top }, 800)
    return copy;
}

function addRowTemplate(table)
{
    var copy = getRowTemplateCopy(table);
    $(copy).insertBefore($('tr.add-template', table)).find('input:first', this).focus();
    return copy;
}

function switchMailProvider(event)
{
    var table = $(this).parent().parent().parent().parent();

    switch ($(this).val())
    {
        case 'default':
            var zone = $(table).find('tr.zone td.value input').val();
            var mx = typeof(dns.mx[zone]) != 'undefined' ? dns.mx[zone] : [];
        break;
        case 'googleapps':
            var mx = [
                [ 'aspmx.l.google.com', 10 ],
                [ 'alt1.aspmx.l.google.com', 20 ],
                [ 'alt2.aspmx.l.google.com', 20 ],
                [ 'aspmx2.googlemail.com', 30 ],
                [ 'aspmx3.googlemail.com', 30 ],
                [ 'aspmx4.googlemail.com', 30 ],
                [ 'aspmx5.googlemail.com', 30 ]
            ];
        break;
        case 'custom':
            var zone = $(table).find('tr.zone td.value input').val();
            var mx = typeof(dns.mx[zone]) != 'undefined' ? dns.mx[zone] : [];
        break;
    }

    $('tr.record', table).each(function(i)
    {
        //alert($(this).attr('class'));
        if (!$(this).hasClass('add-template')) $(this).remove();
    });

    for (var i = 0; i < mx.length; i++)
    {
        var entry = addRowTemplate(table, 'mx');
        entry.find('td.value input').val(mx[i][0]);
        entry.find('td.ttl input').val(mx[i][1]);
    }

    if (mx.length == 0) addRowTemplate(table, 'mx');
}

function buildZoneHTML(dns)
{
    for (var zone in dns.mx)
    {
        var table = addRecordTemplate('mx');
        table.find('tr.zone td.value input').val(zone);
        for (var i = 0; i < dns.mx[zone].length; i++)
        {
            var entry = i > 0 ? addRowTemplate(table) : $('tr.record', table);
            entry.find('td.value input').val(dns.mx[zone][i][0]);
            entry.find('td.ttl input').val(dns.mx[zone][i][1]);
        }
    }

    for (var zone in dns.a)
    {
        var table = addRecordTemplate('a');
        table.find('tr.zone td.value input').val(zone);
        for (var i = 0; i < dns.a[zone].length; i++)
        {
            var entry = i > 0 ? addRowTemplate(table) : $('tr.record', table);
            entry.find('td.value input').val(dns.a[zone][i][0]);
            entry.find('td.ttl input').val(dns.a[zone][i][1]);
        }
    }

    for (var zone in dns.cname)
    {
        var table = addRecordTemplate('cname');
        table.find('tr.zone td.value input').val(zone);
        for (var i = 0; i < dns.cname[zone].length; i++)
        {
            var entry = i > 0 ? addRowTemplate(table) : $('tr.record', table);
            entry.find('td.value input').val(dns.cname[zone][i][0]);
            entry.find('td.ttl input').val(dns.cname[zone][i][1]);
        }
    }
}

var dns = {
    'mx': { },
    'a': { },
    'cname': { },
    'txt': { }
};

$(document).ready(function()
{
    // A
    $('h1:first-child a.add', $('#dns-a')).click(function(event)
    {
        addRecordTemplate('a');

        if (event.preventDefault) event.preventDefault();
        return false;
    });
    $('#dns-a table tbody tr.add-row td a').live('click', function(event)
    {
        var table = $(this).parent().parent().parent();
        var entry = addRowTemplate(table);
        entry.find('td.ttl input').val($('tr.record:first td.ttl input', table).val());

        if (event.preventDefault) event.preventDefault();
        return false;
    });

    // CNAME
    $('h1:first-child a.add', $('#dns-cname')).click(function(event)
    {
        addRecordTemplate('cname');

        if (event.preventDefault) event.preventDefault();
        return false;
    });
    $('#dns-cname table tbody tr.add-row td a').live('click', function(event)
    {
        var table = $(this).parent().parent().parent();
        var entry = addRowTemplate(table);
        entry.find('td.ttl input').val($('tr.record:first td.ttl input', table).val());

        if (event.preventDefault) event.preventDefault();
        return false;
    });

    // MX
    $('h1:first-child a.add', $('#dns-mx')).click(function(event)
    {
        addRecordTemplate('mx');
        if (event.preventDefault) event.preventDefault();
        return false;
    });
    $('#dns-mx table tbody tr.add-row td a').live('click', function(event)
    {
        var table = $(this).parent().parent().parent();
        addRowTemplate(table);
        if (event.preventDefault) event.preventDefault();
        return false;
    });

    $('div.dns-record-type table tbody tr.record td.del a').live('click', function(event)
    {
        var count = $(this).parent().parent().parent().find('tr.record').length - 1;
        if (count == 1) $(this).parent().parent().parent().parent().remove();
        else $(this).parent().parent().remove();
        if (event.preventDefault) event.preventDefault();
        return false;
    });

    buildZoneHTML(dns);

});
