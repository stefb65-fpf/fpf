//profile news switch toggle
let newsSwitchBtn = document.querySelector('.newsletter .switch input')
let newsNotSubscribingLabel = document.querySelector('.newsletter label.notSubscribing')
let newsSubscribingLabel = document.querySelector('.newsletter label.subscribing')
let blacklistDate = document.querySelector('.newsletter .blacklist')

function submitNewsPreferences(preference, personneId) {
    $.ajax({
        url: '/api/submitNewsPreferences',
        type: 'POST',
        data: {
            newspreference: preference,
            personne: personneId
        },
        dataType: 'JSON',
        success: function (data) {
            if (data.length > 0) {
                console.log(data[0] = true)
                $('input[name=newspreference]').parent().find('.message').addClass('show')
                setTimeout(() => {
                    $('input[name=newspreference]').parent().find('.message').removeClass('show')
                }, "2000")
            }
        },
        error: function (e) {
        }
    });
}

if (newsSwitchBtn) {
    newsSwitchBtn.addEventListener('click', function () {
        let personneId = this.dataset.personne
        if (parseInt(this.value)) {
            this.value = 0
            newsNotSubscribingLabel.classList.remove("d-none")
            newsSubscribingLabel.classList.add("d-none")
            if (blacklistDate) {
                blacklistDate.classList.remove("d-none")
            }

        } else {
            this.value = 1
            newsNotSubscribingLabel.classList.add("d-none")
            newsSubscribingLabel.classList.remove("d-none")
            if (blacklistDate) {
                blacklistDate.classList.add("d-none")
            }
        }
        submitNewsPreferences(this.value, personneId)
    })
}
