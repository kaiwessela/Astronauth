var tabs;
var forms;
var currentForm;

var Tab = function(type) {
	this.type = type;
	this.elem = document.getElementById('astro-tab-' + this.type);
	this.link = document.getElementById('astro-tab-link-' + this.type);
}

Tab.prototype.registerEvents = function() {
	this.link.addEventListener('click', function(event){
		event.preventDefault();
		tabs[event.currentTarget.getAttribute('data-type')].onClick();
	});
}

Tab.prototype.getForm = function() {
	return forms[this.type];
}

Tab.prototype.activate = function() {
	this.elem.classList.toggle('disabled', false);
	this.elem.classList.toggle('enabled', false);
	this.elem.classList.toggle('active', true);
}

Tab.prototype.enable = function() {
	this.elem.classList.toggle('disabled', false);
	this.elem.classList.toggle('enabled', true);
	this.elem.classList.toggle('active', false);
}

Tab.prototype.disable = function() {
	this.elem.classList.toggle('disabled', true);
	this.elem.classList.toggle('enabled', false);
	this.elem.classList.toggle('active', false);
}

Tab.prototype.onClick = function() {
	this.activate();
	this.getForm().activate();
}

var Form = function(type) {
	this.type = type;
	this.elem = document.getElementById('astro-' + this.type);
	this.form = document.getElementById('astro-' + this.type + '-form');
	this.inputs;
}

Form.prototype.registerEvents = function() {
	this.form.addEventListener('submit', function(event){
		event.preventDefault();
		forms[event.currentTarget.getAttribute('data-type')].onSubmit();
	});
}

Form.prototype.activate = function() {
	if(currentForm !== ''){
		forms[curentForm].deactivate();
	}
	this.elem.classList.toggle('active', true);
}

Form.prototype.deactivate = function() {
	this.elem.classList.toggle('active', false);
}

Form.prototype.onSubmit = function() {

}

var Input = function(type, formType) {
	this.type = type;
	this.formType = formType;
	this.elem = document.getElementById('astro-' + this.formType + '-' + this.type);
	this.value = this.elem.value;
	this.error = [];
}

Input.prototype.registerEvents = function() {
	this.elem.addEventListener('input', function(event){
		forms[event.currentTarget.parent].inputs[this.elem.name].onInput();
		// .parent correct?
	});
}

Input.prototype.onInput = function() {
	if(this.value.length == 0){
		this.elem.classList.toggle('valid', false);
		this.elem.classList.toggle('invalid', false);
	} else if(this.validate() == true){
		this.elem.classList.toggle('valid', true);
		this.elem.classList.toggle('invalid', false);
	} else {
		this.elem.classList.toggle('valid', false);
		this.elem.classList.toggle('invalid', true);
	}
}

Input.prototype.validate = function() {
	this.error = [];
	var s = true;
	if(this.type == 'user'){
		if(this.value.length < 3){
			this.error[] = 'user.invalid.length';
			s = false;
		}
	} else if(this.type == 'username'){
		if(this.value.length < 3 || this.value.length > 20){
			this.error[] = 'username.invalid.length';
			s = false;
		}

		if(!this.value.match(/^[a-zA-Z0-9-_.]*$/)){
			this.error[] = 'username.invalid.characters';
			s = false;
		}
	} else if(this.type == 'email'){
		if(!this.value.match(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/)){
			this.error[] = 'email.invalid.characters';
			s = false;
		}
	} else if(this.type == 'password'){
		if(this.value.length < 8 || this.value.length > 72){
			this.error[] = 'password.invalid.length';
			s = false;
		}
	} else if(this.type == 'pwcheck'){
		if(forms[this.formType].inputs.password.value != this.value){
			this.error[] = 'password.doesntmatch';
			s = false;
		}
	} else if(this.type == 'keepLoggedIn'){
		s = true;
	} else {
		s = false;
	}

	return $s;
}
