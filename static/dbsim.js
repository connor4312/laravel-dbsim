var doSubmit = function() {
    $.ajax({
        url: './',
        dataType: 'json',
        type: 'POST',
        data: { q: $('#q').val() }
    }).done(function(data) {
        data.query = $('<div />').text(data.query).html();
        data.query = hljs.highlightAuto(data.query).value;

        $('#query_target').html(data.query);
        $('#binding_target').text(JSON.stringify(data.bindings));
    });
};

var timeout;

$('#q').focus()
       .elastic()
       .on('keydown', function() {
           clearTimeout(timeout);
           timeout = setTimeout(doSubmit, 1000);
       });