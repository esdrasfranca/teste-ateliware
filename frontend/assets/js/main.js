var url = 'http://localhost:8000/api/';
var next = null;
var listModal = [];
var pokemon1 = null;
var pokemon2 = null;
var cardRef = null;

$(document).ready(function () {
    $('#btnLutar').click(function () {
        if (pokemon1 && pokemon2) {
            $('#btnLutar').prop('disabled', true);
            batalhar();
        } else {
            alert('Escolha os adversários para a batalha!!!');
        }
    });

    $("#btnMaisResultados").on('click', function () {
        const text = $(this).text();
        $(this).prop('disabled', true).text('Carregando...');

        $.get(next, function (response) {
            next = response.next;
            listModal = [...listModal, ...response.data];
            changeList();
            $('#btnMaisResultados').prop('disabled', false).text(text);
        }, "json");
    })

    // $('#modalPesquisa').on('hide.bs.modal', function () {
    //     $('#list-group-prokemons').html('')
    //     cardRef = null;
    // })

    $('#modalPesquisa').on('hide.bs.modal', function () {
        $('#list-group-prokemons').html('');
    });

    $('#modalPesquisa').on('hidden.bs.modal', function () {
        cardRef = null;
    });

    $('#modalResultado').on('hidden.bs.modal', function () {
        $('#btnLutar').prop('disabled', false);
    })

    $(".card-pokemon").on('click', function () {
        cardRef = $(this).data('card-ref')

        if (listModal.length == 0) {
            if (!next) {
                next = `${url}pokemons?page=0`
            }

            $.ajax({
                url: next,
                method: "GET",
                dataType: "json",
                success: function (response) {
                    next = response.next
                    listModal = response.data;
                    changeList()
                },
                error: function (response) {
                    alert(response.responseJSON.message)
                }
            })
        } else {
            changeList()
        }
    })
});

function changeList() {
    $('#modalPesquisa').modal('show');
    $('#list-group-prokemons').html(null);

    $.each(listModal, (i, element) => {
        let active = "";

        if ((element.name == pokemon1 && cardRef == 'card-1') || (element.name == pokemon2 && cardRef == 'card-2')) {
            active = "active";
        }

        $('<button>', {
            type: 'button',
            class: `list-group-item list-group-item-action text-capitalize fw-bold ${active}`,
            'aria-current': 'true',
            text: element.name,
            'data-value': element.name,
            'data-url': element.url,
            click: function () {
                $(this).blur();
                selectPokemon(this);
                $('#modalPesquisa').modal('hide');
            }
        }).appendTo('#list-group-prokemons');
    });
}

function selectPokemon(element) {
    const selected = $(element).data('value');
    const url = $(element).data('url');
    const currentCard = cardRef;

    if (currentCard == 'card-1') {
        pokemon1 = selected;
    } else {
        pokemon2 = selected;
    }

    $(`[data-card-ref="${currentCard}"]`).html('');

    $.get(url)
        .done(function (response) {
            $(`[data-card-ref="${currentCard}"]`).html(`<img src="${response.data.sprite}"/>`);
            $(`[data-card-name="${currentCard}"]`).html(response.data.name);
        })
        .fail(function (error) {
            alert(error.responseJSON.message);
            $(`[data-card-ref="${currentCard}"]`).html('<span class="text-muted text-center">Clique para escolher...</span>');
            console.error("Erro ao buscar dados para o card " + currentCard, error);
        });
}

function batalhar() {

    $('#img-pokemon').html(null)

    $.get(`${url}battle?pokemon1=${pokemon1}&pokemon2=${pokemon2}`)
        .done(function (response) {
            $("#mensagem-resultado").html(response.data.message)

            if (response.data.winner) {
                $('<img>', {
                    class: "img-resultado",
                    src: response.data.winner.sprite,
                }).appendTo('#img-pokemon');
            }

            $('#modalResultado').modal('show')
        })
        .fail(function () { });
}