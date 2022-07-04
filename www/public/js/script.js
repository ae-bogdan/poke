$(function() {
    function getNewRow(baseRow) {
        return element = baseRow
            .clone()
            .removeClass('d-none base-row')
            .addClass('pokemon-row')
        ;
    }
    function fetchTeamList(data) {
        $('.pokemon-row').remove();
        $.ajax({
            type: 'post',
            url: '/team/fetch',
            data: data,
            success: function(result) {
                var baseRow = $('.base-row');
                var element = '';
                var teamsCount = Object.values(result).length;
                if (teamsCount === 0) {
                    element = getNewRow(baseRow);
                    element.html('<td colspan="5" class="text-center">No entries found :(</td>');
                    baseRow.after(element);
                }
                for (var teamId in result) {
                    if (result.hasOwnProperty(teamId)) {
                        var pokemonPictures = '';
                        var pokemonTypes = [];
                        for (var pokemonId in result[teamId].pokemon) {
                            if (result[teamId].pokemon.hasOwnProperty(pokemonId)) {
                                var picture = '<img src="https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/' + result[teamId].pokemon[pokemonId].pokemon_id + '.png" alt="' + result[teamId].pokemon[pokemonId].pokemon_name + '">';
                                pokemonPictures += picture;

                                for (var typeId in result[teamId].pokemon[pokemonId].type) {
                                    if (result[teamId].pokemon[pokemonId].type.hasOwnProperty(typeId)) {
                                        var type = result[teamId].pokemon[pokemonId].type[typeId].type_name;
                                        if (pokemonTypes.indexOf(type) === -1) {
                                            pokemonTypes.push(type);
                                        }
                                    }
                                }
                            }
                        }
                        element = getNewRow(baseRow);
                        element.find('.team-name').text(result[teamId].team_name)
                        element.find('.pokemons').html(pokemonPictures)
                        element.find('.total-experience').text(result[teamId].sum_exp)
                        element.find('.types').text(pokemonTypes.join(', '));
                        element.find('.edit-team').html('<a href="/team/' + teamId + '/edit">Edit</a>');
                        baseRow.after(element);
                    }
                }
            }
        });
    }
    function fetchPokemon() {
        $.get('/pokemon/catch',
            function(data) {
                $('#submit-pokemon').removeClass('d-none');
                $('.get-pokemon-intro').text('You caught...');
                $('.get-pokemon-message').text('Wanna try again?');
                $('.pokemon-container').removeClass('d-none');
                $('.pokemon-name').text(data.name + '!');
                $('.pokemon-base-exp').text(data.base_exp);
                $('.pokemon-sprite').attr('src', data.image);
                $('.pokemon-abilities').text(data.abilities.join(', ').replace(/-/g, ' '));
                $('.pokemon-types').text(Object.values(data.types).join(', ').replace(/-/g, ' '));
                // set actual values
                $('#pokemon_pokemon_id').val(data.id);
                $('#pokemon_name').val(data.name);
                $('#pokemon_exp').val(data.base_exp);
                $('#pokemon_type option').each(function(index, element) {
                    if (Object.keys(data.types).indexOf($(element).val()) > -1) {
                        $(element).attr('selected', true);
                    } else {
                        $(element).removeAttr('selected');
                    }
                });
            }
        );
    }
    $('#pokemon_team_name').on('focus', function() {
        $('.choose-team-alert').addClass('d-none');
    });
    $('#get-pokemon').on('click', function() {
        if ($('#pokemon_team_name').val() === '') {
            $('.choose-team-alert').removeClass('d-none');
            return;
        }
        fetchPokemon();
    });
    $('#form-filter-type').on('change', function(event) {
        fetchTeamList($(event.currentTarget).serializeArray());
    });
    fetchTeamList([]);
});
