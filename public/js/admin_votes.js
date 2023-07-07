$('#typeVote').on('change', function () {
    if ($('#typeVote option:selected').val() == -1) {
        $('#bloc3phases').hide()
        $('#blocclassique').hide()
        $('#validateVote').hide()
    }
    if ($('#typeVote option:selected').val() == 0) {
        $('#bloc3phases').hide()
        $('#blocclassique').show()
        $('#validateVote').show()
    }
    if ($('#typeVote option:selected').val() == 1) {
        $('#bloc3phases').show()
        $('#blocclassique').hide()
        $('#validateVote').show()
    }
})

$('#typeElection').on('change', function () {
    if ($('#typeElection option:selected').val() == 1) {
        $('#nbPostesElection').hide()
    } else {
        $('#nbPostesElection').show()
    }
})
