$(function () {
  function submitState(enabled) {
  	//This forces the user to change password everytime he wants to edit his profile... >:(
    //$('[type=submit]').attr('disabled', !enabled)
  }
  submitState(false)
  $('#pw, #un').val("")
  $('#pw2, [name=repeatpassword]').after('<div id="note">')
  $('#pw, #password').on('input', function self() {
    if (typeof zxcvbn !== 'function') {
      submitState(true)
      var that = this
      setTimeout(function () {
         self.call(that)
      }, 50)
    }
    if (!$(this).val()) {
      submitState(!$('#pw2').length)
      $('#note').html("")
      return
    }
    // abxd, acmlmboard, acmlm are special words that could happen
    // considering it's the name of this software
    var result = zxcvbn($(this).val(), ['abxd', 'acmlmboard', 'acmlm'])
    var message = ""
    submitState(true)
    if (result.score <= 2) {
      if (result.score <= 1) {
        if (result.score)
          message = 'Your password is too dangerous. '
        else
          message = 'Your password is unbelievably dangerous. '
        submitState(false)
      }
      else
        message = 'Your password may be dangerous. '
      var time = result.crack_time_display
      if (time === 'instant')
          message += 'It could be guessed automatically almost instantly.'
      else
          message += 'It would take ' + time + ' to guess it.'
    }
    $('#note').html('<small>' + message + '</small>')
  })
})
