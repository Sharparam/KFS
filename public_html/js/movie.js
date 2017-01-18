function formatResult(state, element) {
  if (state.disabled || state.loading)
    return state.text;
  return state.title + ' (' + state.release_date.substring(0, 4) + ')';
}

function formatSelection(state, element) {
  if (state.title == '')
    return;

  return state.title + ' (' + state.release_date.substring(0, 4) + ')';
}

$('#tmdb').select2({
  ajax: {
    url: '/tmdb.php',
    dataType: 'json',
    delay: 500,
    data: function (params) {
      return {
        q: params.term,
        t: 'search'
      };
    },
    processResults: function (data, params) {
      return {
        results: data.results
      };
    },
    cache: true,
    placeholder: 'Search for movie'
  },
  minimumInputLength: 2,
  templateResult: formatResult,
  templateSelection: formatSelection
});

$('#tmdb').on('select2:select', function (e, a, b, c) {
  var data = e.params.data;
  var id = data.id;

  if (!id)
    return;

  $.ajax({
    url: '/tmdb.php',
    data: {
      t: 'get',
      q: id
    },
    dataType: 'json',
    success: function (data) {
      $('#title').val(data.title);
      $('#original').val(data.original_title);
      $('#genre').val(data.genres[0].name);
      $('#country').val(data.production_countries[0].name);
      var director = data.credits.crew.find(function (p) { return p.job == 'Director'; });
      if (director)
        $('#director').val(director.name);
      $('#year').val(data.release_date.substring(0, 4));
      $('#duration').val(data.runtime);
      $('#imdb').val('http://www.imdb.com/title/' + data.imdb_id);
      $('#description').val(data.overview);
    }
  });
});
