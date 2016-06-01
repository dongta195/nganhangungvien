<!-- BEGIN PAGE HEADER-->
<!-- BEGIN THEME PANEL -->
<div class="theme-panel">
	<div class="toggler tooltips" data-container="body" data-placement="left" data-html="true" data-original-title="Click to open advance theme customizer panel">
		<i class="icon-settings"></i>
	</div>
	<div class="toggler-close">
		<i class="icon-close"></i>
	</div>
	<div class="theme-options">
		<div class="theme-option theme-colors clearfix">
			<span> THEME COLOR </span>
			<ul>
				<li class="color-default current tooltips" data-style="default" data-container="body" data-original-title="Default"> </li>
				<li class="color-grey tooltips" data-style="grey" data-container="body" data-original-title="Grey"> </li>
				<li class="color-blue tooltips" data-style="blue" data-container="body" data-original-title="Blue"> </li>
				<li class="color-dark tooltips" data-style="dark" data-container="body" data-original-title="Dark"> </li>
				<li class="color-light tooltips" data-style="light" data-container="body" data-original-title="Light"> </li>
			</ul>
		</div>
		<div class="theme-option">
			<span> Theme Style </span>
			<select class="layout-style-option form-control input-small">
				<option value="square" selected="selected">Square corners</option>
				<option value="rounded">Rounded corners</option>
			</select>
		</div>
		<div class="theme-option">
			<span> Layout </span>
			<select class="layout-option form-control input-small">
				<option value="fluid" selected="selected">Fluid</option>
				<option value="boxed">Boxed</option>
			</select>
		</div>
		<div class="theme-option">
			<span> Header </span>
			<select class="page-header-option form-control input-small">
				<option value="fixed" selected="selected">Fixed</option>
				<option value="default">Default</option>
			</select>
		</div>
		<div class="theme-option">
			<span> Top Dropdown</span>
			<select class="page-header-top-dropdown-style-option form-control input-small">
				<option value="light" selected="selected">Light</option>
				<option value="dark">Dark</option>
			</select>
		</div>
		<div class="theme-option">
			<span> Sidebar Mode</span>
			<select class="sidebar-option form-control input-small">
				<option value="fixed">Fixed</option>
				<option value="default" selected="selected">Default</option>
			</select>
		</div>
		<div class="theme-option">
			<span> Sidebar Style</span>
			<select class="sidebar-style-option form-control input-small">
				<option value="default" selected="selected">Default</option>
				<option value="compact">Compact</option>
			</select>
		</div>
		<div class="theme-option">
			<span> Sidebar Menu </span>
			<select class="sidebar-menu-option form-control input-small">
				<option value="accordion" selected="selected">Accordion</option>
				<option value="hover">Hover</option>
			</select>
		</div>
		<div class="theme-option">
			<span> Sidebar Position </span>
			<select class="sidebar-pos-option form-control input-small">
				<option value="left" selected="selected">Left</option>
				<option value="right">Right</option>
			</select>
		</div>
		<div class="theme-option">
			<span> Footer </span>
			<select class="page-footer-option form-control input-small">
				<option value="fixed">Fixed</option>
				<option value="default" selected="selected">Default</option>
			</select>
		</div>
	</div>
</div>
<!-- END THEME PANEL -->
<h3 class="page-title">
	{{isset($pageTitle) ? $pageTitle : 'Dashboard'}}
</h3>
<div class="page-bar">
	<ul class="page-breadcrumb">
		<li>
			<i class="icon-home"></i>
			<a href="/admin">Home</a>
			<i class="fa fa-angle-right"></i>
		</li>
		<li>
			<span>{{isset($pageTitle) ? $pageTitle : 'Dashboard'}}</span>
		</li>
	</ul>
	<div class="page-toolbar">
		<div class="btn-group pull-right">
			<button type="button" class="btn btn-fit-height grey-salt dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true"> Actions
				<i class="fa fa-angle-down"></i>
			</button>
			<ul class="dropdown-menu pull-right" role="menu">
				<li>
					<a href="#">
						<i class="icon-bell"></i> Action</a>
				</li>
				<li>
					<a href="#">
						<i class="icon-shield"></i> Another action</a>
				</li>
				<li>
					<a href="#">
						<i class="icon-user"></i> Something else here</a>
				</li>
				<li class="divider"> </li>
				<li>
					<a href="#">
						<i class="icon-bag"></i> Separated link</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<!-- END PAGE HEADER-->