<?php if(!defined('BASEPATH')) exit('Direct Access Not Allowed');

$config['per_page'] = 5;
$config['query_string_segment'] = 'page';
$config['full_tag_open'] = '<nav aria-label="Page navigation example"><ul class="pagination">';
$config['full_tag_close'] = '</ul></nav><!--pagination-->';
$config['first_link'] = '&laquo; First';
$config['first_tag_open'] = '<li class="prev page">';
$config['first_tag_close'] = '</li>';
$config['last_link'] = 'Last &raquo;';
$config['last_tag_open'] = '<li class="next page">';
$config['last_tag_close'] = '</li>';
$config['next_link'] = 'Next &rarr;';
$config['next_tag_open'] = '<li class="next page">';
$config['next_tag_close'] = '</li>';
$config['prev_link'] = '&larr; Previous';
$config['prev_tag_open'] = '<li class="prev page">';
$config['prev_tag_close'] = '</li>';
$config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
$config['cur_tag_close'] = '</a></li>';
$config['num_tag_open'] = '<li class="page-item">';
$config['num_tag_close'] = '</li>';
/*$config['first_link'] = 'First';
$config['first_tag_open'] = '<div>';
$config['first_tag_close'] = '</div>';
$config['first_url'] = '';
$config['last_link'] = 'Last';
$config['last_tag_open'] = '<div>';
$config['last_tag_close'] = '</div>';*/
$config['display_pages'] = TRUE;
// 
$config['attributes'] = array('class' => 'page-link');
// end of file Pagination.php 
// Location config/pagination.php 