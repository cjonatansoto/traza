<li class="nav-item dropdown {{ Request::is('users') || Request::is('roles') ? 'active' : '' }}">
    <a href="#" class="nav-link has-dropdown"><i class="fa fa-shield-alt"></i> <span>SEGURIDAD</span></a>
    <ul class="dropdown-menu" style="display: none;">
        <li class="{{ Request::is('users') ? 'active' : '' }}"><a class="nav-link" href="{{route('users.index')}}">USUARIOS</a></li>
        <li class="mt-1 {{ Request::is('roles') ? 'active' : '' }}"><a class="nav-link" href="{{route('roles.index')}}">ROLES</a></li>
    </ul>
</li>
<li class="nav-item dropdown {{ Request::is('countries') || Request::is('shippingports') || Request::is('destinationports') || Request::is('slaughterplaces') || Request::is('exporters') || Request::is('bordercrossings') || Request::is('storagelocations') || Request::is('consignees') ? 'active' : '' }}">
    <a href="#" class="nav-link has-dropdown"><i class="fa fa-database"></i> <span>MANT. DATOS</span></a>
    <ul class="dropdown-menu" style="display: none;">
        <li class="{{ Request::is('countries') ? 'active' : '' }}"><a class="nav-link" href="{{route('countries.index')}}">PAÍSES</a></li>
        <li class="mt-1 {{ Request::is('shippingports') ? 'active' : '' }}"><a class="nav-link" href="{{route('shippingports.index')}}">PUERTOS DE EMBARQUE</a></li>
        <li class="mt-1 {{ Request::is('destinationports') ? 'active' : '' }}"><a class="nav-link" href="{{route('destinationports.index')}}">PUERTOS DE DESTINO</a></li>
        <li class="mt-1 {{ Request::is('slaughterplaces') ? 'active' : '' }}"><a class="nav-link" href="{{route('slaughterplaces.index')}}">LUGARES DE FAENA</a></li>
        <li class="mt-1 {{ Request::is('storagelocations') ? 'active' : '' }}"><a class="nav-link" href="{{route('storagelocations.index')}}">LUGARES DE ALMACENAMIENTO</a></li>
        <li class="mt-1 {{ Request::is('exporters') ? 'active' : '' }}"><a class="nav-link" href="{{route('exporters.index')}}">EXPORTADORES</a></li>
        <li class="mt-1 {{ Request::is('bordercrossings') ? 'active' : '' }}"><a class="nav-link" href="{{route('bordercrossings.index')}}">ADUANAS</a></li>
        <li class="mt-1 {{ Request::is('consignees') ? 'active' : '' }}"><a class="nav-link" href="{{route('consignees.index')}}">CONSIGNATARIOS</a></li>
    </ul>
</li>
<li class="nav-item dropdown {{ Request::is('neppex/create') ? 'active' : '' }}">
    <a href="#" class="nav-link has-dropdown"><i class="fa fa-database"></i> <span>NEPPEX</span></a>
    <ul class="dropdown-menu" style="display: none;">
        <li class="{{ Request::is('neppex') ? 'active' : '' }}"><a class="nav-link" href="{{route('neppex.index')}}">PACKINGS</a></li>
        <li class="{{ Request::is('neppex/create') ? 'active' : '' }}"><a class="nav-link" href="{{route('neppex.create')}}">SUBIR TXT</a></li>
    </ul>
</li>
