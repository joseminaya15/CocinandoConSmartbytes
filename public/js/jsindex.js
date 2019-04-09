var $win = $(window);
$win.scroll(function () {
	if ($win.scrollTop() > 45) {
		$(".js-header").addClass("navbarcolor");
	} else {
		$(".js-header").removeClass("navbarcolor");
	}
});
$('a.link[href^="#"]').click(function(e) {
 	var target = $(this).attr('href');
 	var strip = target.slice(1);
 	var anchor = $("section[id='" + strip + "']");
 	e.preventDefault();
 	y = (anchor.offset() || {
 		"top" : NaN
 	}).top;
 	$('html, body').animate({
 		scrollTop : (y - 40)
 	}, 'slow');
});
$('#home .owl-carousel').owlCarousel({
	lazyLoad : true,
	animateOut: 'fadeOut',
	animateIn: 'fadeIn',
	responsive : {
		0 : {
			items : 1
		}
	},
	navigation : false,
	nav : false,
	loop : true,
	autoplay : true,
	mouseDrag: false,
	dots: false,
	autoplayTimeout : 3000
});
function sendInformation(){
	var check_burger = null;
	var company 	 = $('#company').val();
	var direccion  	 = $('#address').val();
	var name 		 = $('#name').val();
	var surname 	 = $('#surname').val();
	var position 	 = $('#position').val();
	var email 		 = $('#email').val();
	var phone 		 = $('#phone').val();
	var birthday     = $('#birthday').val();
	var deporte      = $('#sport').val();
	var burger1      = $('#burger-1').is(':checked');
	var burger2		 = $('#burger-2').is(':checked');
	var burger3 	 = $('#burger-3').is(':checked');
	var burger4		 = $('#burger-4').is(':checked');
	// var comercializa = $('#commercialization').val();
	// var description  = $('#description').val();
	if(company == null || company == '') {
		msj('error', 'Empresa debe completarse');
		return;
	}
	if(direccion == null || direccion == '') {
		msj('error', 'Direccion debe completarse');
		return;
	}
	if(name == null || name == '') {
		msj('error', 'Nombre debe completarse');
		return;
	}
	if(surname == null || surname == '') {
		msj('error', 'Apellido debe completarse');
		return;
	}
	if(position == null || position == '') {
		msj('error', 'Cargo debe completarse');
		return;
	}
	if(email == null || email == '') {
		msj('error', 'Email debe completarse');
		return;
	}
	if(!validateEmail(email)){
		msj('error', 'El formato de email es incorrecto');
		return;
	}
	if(validateEmailCorporative(email)){
      	msj('error', 'Ingrese un email corporativo');
		return;
	}
	if(phone == null || phone == '') {
		msj('error', 'Teléfono debe completarse');
		return;
	}
	if(birthday == null || birthday == '') {
		msj('error', 'Cumpleaños debe completarse');
		return;
	}
	if(burger1 == true){
		check_burger = 1;
	}else if(burger2 == true) {
		check_burger = 2;
	}else if(burger3 == true) {
		check_burger = 3;
	}else if(burger4 == true) {
		check_burger = 4;
	}
	$.ajax({
		data : {Company      : company,
			    Direccion    : direccion,
				Name	     : name,
				Surname	     : surname,
				Position     : position,
				Email 	     : email,
				Phone	     : phone,
				Birthday	 : birthday,
				Deporte   	 : deporte,
				Burger	     : check_burger},
		url  : 'home/register',
		type : 'POST'
	}).done(function(data){
		try {
			data = JSON.parse(data);
			if(data.error == 0){
				$('.js-input').find('input').val('');
				$('.js-checkbox').find('.mdl-checkbox').removeClass('is-checked');
				$('.js-checkbox').find('input').prop("checked", false);
				msj('success', data.msj);
				$('#confirmation').addClass('aparecer');
        	}else{
        		msj('error', data.msj);
        		return;
        	}
		} catch (err) {
			msj('error', err.message);
		}
	});
}
function ingresarQuiz(){
	var correo = $('#emailRegister').val(); 
	if(correo == null || correo == '') {
		msj('error', 'Email debe completarse');
		return;
	}
	if(!validateEmail(correo)){
		msj('error', 'El formato de email es incorrecto');
		return;
	}
	$.ajax({
		data  : { correo : correo},
		url   : 'home/ingresar',
		type  : 'POST'
	}).done(function(data){
		try{
        	data = JSON.parse(data);
        	if(data.error == 0){
				$('#emailRegister').val("");
				$('#ModalQuiz').modal('show');
        	}else {
				msj('error', 'Email no registrado');
        		return;
        	}
		} catch (err){
			msj('error',err.message);
		}
	});
}
function nextQuestion(element){
	var question = element.attr('data-id');
	var section = element.parents('.jm-question');
	$('.jm-question').removeClass('fadeInRight');
	section.addClass('animated fadeOutLeft');
	$('#question-'+question).addClass('animated fadeInRight')
}
function sendQuiz(){
	var pregunta1 = $('input[name="options1"]:checked').val();
	var pregunta2 = $('input[name="options2"]:checked').val();
	var pregunta3 = $('input[name="options3"]:checked').val();
	var pregunta4 = $('input[name="options4"]:checked').val();
	var pregunta5 = $('input[name="options5"]:checked').val();
	var caracteres = "abcdefghijkmnpqrtuvwxyzABCDEFGHJKMNPQRTUVWXYZ2346789";
	var codigo = "";
	for (i=0; i<6; i++) codigo +=caracteres.charAt(Math.floor(Math.random()*caracteres.length)); 
	$.ajax({
		data : {Pregunta1   	  : pregunta1,
				Pregunta2		  : pregunta2,
				Pregunta3		  : pregunta3,
				Pregunta4   	  : pregunta4,
				Pregunta5 		  : pregunta5,
				Codigo            : codigo},
		url   : 'home/quiz',
		type  : 'POST'
	}).done(function(data){
		try{
        	data = JSON.parse(data);
        	if(data.error == 0){
				$('.js-checkbox').find('.mdl-checkbox').removeClass('is-checked');
				$('.js-checkbox').find('input').prop("checked", false);
				$('#textAnswer').css('display','none');
				$('.jm-question').removeClass('fadeInRight');
				$('#question-seven').addClass('animated fadeOutLeft');
				$('#section-cupo').addClass('animated fadeInRight');
        	}else {
				msj('error', 'Hubo un problema');
        		return;
        	}
		} catch (err){
			msj('error',err.message);
		}
	});
}
function validateEmail(email){
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function validateEmailCorporative(email){
    var re = /[a-zA-Z0-9@]+(?=hotmail.com|yahoo.com|gmail.com|outlook.com)/;
    return re.test(email);
}
function verificarDatos(e) {
	if(e.keyCode === 13){
		e.preventDefault();
		ingresarQuiz();
    }
}
function checkedOtros(element){
	var isChecked    = element;
	var idChecked    = isChecked.attr('id');
	var divInput     = element.parents('.jm-list--checkbox').find('.jm-input');
	console.log(idChecked);
	if(element.is(":checked")){
		divInput.css('display','block');
	}else{
		divInput.css('display','none');
	}
}
function reload(){
	location.reload();
}
function openModalLibro(id){
	var id = $('#'+id);
	var modalTeam = $('#ModalLibro');
	var tituloModal = id.parents('.jm-book').find('h2');
	var descripcion = id.parents('.jm-book').find('p');
	var imagen      = id.parents('.jm-book').find('img');
	modalTeam.find('img').attr("src",imagen.attr('src'));
	modalTeam.find('h2').text(tituloModal[0].innerText);
	modalTeam.find('p').text(descripcion[0].innerText);
	modalTeam.modal('show');
}