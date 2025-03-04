// Import CSS
import './css/style.css';
import './styles/app.scss';

// Import JavaScript
import $ from 'jquery';
import 'bootstrap'; // Bootstrap JavaScript
import 'bootstrap/dist/js/bootstrap.bundle.min.js'; // Bootstrap Bundle (includes Popper)
import 'bootstrap-icons/font/bootstrap-icons.css'; // Bootstrap Icons
import 'bootstrap-select/dist/js/bootstrap-select.min.js'; // Bootstrap Select
import 'select2/dist/js/select2.full.min'; // Select2
import 'select2/dist/css/select2.min.css'; // Select2 CSS
import 'tinymce/tinymce.min.js'; // TinyMCE
import 'boxicons/css/boxicons.min.css';
import 'quill/dist/quill.bubble.css';
import 'remixicon/fonts/remixicon.css';
import 'simple-datatables/dist/style.css';


// Initialize plugins
$(document).ready(function() {
    // Initialize Selectpicker
    $('.selectpicker').selectpicker();

    // Initialize Select2
    $('.select2').select2();
});
