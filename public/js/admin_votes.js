$('#typeVote').on('change', function () {
    if ($('#typeVote option:selected').val() == -1) {
        $('#bloc3phases').hide()
        $('#bloc2phases').hide()
        $('#bloc3phasesexclusif').hide()
        $('#blocclassique').hide()
        $('#validateVote').hide()
    }
    if ($('#typeVote option:selected').val() == 0) {
        $('#bloc3phases').hide()
        $('#bloc2phases').hide()
        $('#bloc3phasesexclusif').hide()
        $('#blocclassique').show()
        $('#validateVote').show()
    }
    if ($('#typeVote option:selected').val() == 1) {
        $('#bloc3phasesexclusif').show()
        $('#bloc3phases').show()
        $('#bloc2phases').hide()
        $('#blocclassique').hide()
        $('#validateVote').show()
    }
    if ($('#typeVote option:selected').val() == 2) {
        $('#bloc3phases').show()
        $('#bloc2phases').show()
        $('#bloc3phasesexclusif').hide()
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
