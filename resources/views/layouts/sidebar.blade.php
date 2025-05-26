<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            {{-- Common Dashboard Menu Item for All Users --}}
            <li>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="menu-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2" stroke="#90959F" stroke-width="2"/>
                            <path d="M3 9H21" stroke="#90959F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 21V9" stroke="#90959F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            {{-- Admin Menu Items --}}
            @if(auth()->user()->role === 'administrator')
            <li>
                <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
                    <div class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#90959f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <span class="nav-text">User Management</span>
                </a>
            </li>
            <li>
                <a href="{{ route('templates.index') }}" class="{{ request()->routeIs('templates.*') ? 'active' : '' }}">
                    <div class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#90959f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <span class="nav-text">Template Dokumen</span>
                </a>
            </li>
            @endif

            {{-- Kriteria Menu - Available for all roles --}}
            <li>
                <a class="has-arrow {{ request()->routeIs('kriteria.*') ? 'active' : '' }}" href="javascript:void(0);" aria-expanded="false">
                    <div class="menu-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <g opacity="0.5">
                                <path opacity="0.4"
                                    d="M16.191 2H7.81C4.77 2 3 3.78 3 6.83V17.16C3 20.26 4.77 22 7.81 22H16.191C19.28 22 21 20.26 21 17.16V6.83C21 3.78 19.28 2 16.191 2Z"
                                    fill="white" />
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M8.08002 6.64999V6.65999C7.64902 6.65999 7.30002 7.00999 7.30002 7.43999C7.30002 7.86999 7.64902 8.21999 8.08002 8.21999H11.069C11.5 8.21999 11.85 7.86999 11.85 7.42899C11.85 6.99999 11.5 6.64999 11.069 6.64999H8.08002ZM15.92 12.74H8.08002C7.64902 12.74 7.30002 12.39 7.30002 11.96C7.30002 11.53 7.64902 11.179 8.08002 11.179H15.92C16.35 11.179 16.7 11.53 16.7 11.96C16.7 12.39 16.35 12.74 15.92 12.74ZM15.92 17.31H8.08002C7.78002 17.35 7.49002 17.2 7.33002 16.95C7.17002 16.69 7.17002 16.36 7.33002 16.11C7.49002 15.85 7.78002 15.71 8.08002 15.74H15.92C16.319 15.78 16.62 16.12 16.62 16.53C16.62 16.929 16.319 17.27 15.92 17.31Z"
                                    fill="white" />
                            </g>
                        </svg>
                    </div>
                    <span class="nav-text">Kriteria</span>
                </a>
                <ul aria-expanded="false">
                    @if(auth()->user()->role === 'administrator')
                    {{-- Admin sees all kriteria --}}
                    <li><a href="{{ route('kriteria.show', 1) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 1 ? 'active' : '' }}">Kriteria 1</a></li>
                    <li><a href="{{ route('kriteria.show', 2) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 2 ? 'active' : '' }}">Kriteria 2</a></li>
                    <li><a href="{{ route('kriteria.show', 3) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 3 ? 'active' : '' }}">Kriteria 3</a></li>
                    <li><a href="{{ route('kriteria.show', 4) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 4 ? 'active' : '' }}">Kriteria 4</a></li>
                    <li><a href="{{ route('kriteria.show', 5) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 5 ? 'active' : '' }}">Kriteria 5</a></li>
                    <li><a href="{{ route('kriteria.show', 6) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 6 ? 'active' : '' }}">Kriteria 6</a></li>
                    <li><a href="{{ route('kriteria.show', 7) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 7 ? 'active' : '' }}">Kriteria 7</a></li>
                    <li><a href="{{ route('kriteria.show', 8) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 8 ? 'active' : '' }}">Kriteria 8</a></li>
                    <li><a href="{{ route('kriteria.show', 9) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 9 ? 'active' : '' }}">Kriteria 9</a></li>
                    @endif

                    {{-- Dosen1 Menu Items - Only Kriteria 1-3 --}}
                    @if(auth()->user()->role === 'dosen1')
                    <li><a href="{{ route('kriteria.show', 1) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 1 ? 'active' : '' }}">Kriteria 1</a></li>
                    <li><a href="{{ route('kriteria.show', 2) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 2 ? 'active' : '' }}">Kriteria 2</a></li>
                    <li><a href="{{ route('kriteria.show', 3) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 3 ? 'active' : '' }}">Kriteria 3</a></li>
            @endif

            {{-- Dosen2 Menu Items - Only Kriteria 4-6 --}}
            @if(auth()->user()->role === 'dosen2')
                    <li><a href="{{ route('kriteria.show', 4) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 4 ? 'active' : '' }}">Kriteria 4</a></li>
                    <li><a href="{{ route('kriteria.show', 5) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 5 ? 'active' : '' }}">Kriteria 5</a></li>
                    <li><a href="{{ route('kriteria.show', 6) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 6 ? 'active' : '' }}">Kriteria 6</a></li>
                    @endif

                    {{-- Dosen3 Menu Items - Only Kriteria 7-9 --}}
                    @if(auth()->user()->role === 'dosen3')
                    <li><a href="{{ route('kriteria.show', 7) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 7 ? 'active' : '' }}">Kriteria 7</a></li>
                    <li><a href="{{ route('kriteria.show', 8) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 8 ? 'active' : '' }}">Kriteria 8</a></li>
                    <li><a href="{{ route('kriteria.show', 9) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 9 ? 'active' : '' }}">Kriteria 9</a></li>
                    @endif

                    {{-- Kaprodi, Kajur, KJM, Koordinator see all criteria --}}
                    @if(in_array(auth()->user()->role, ['kaprodi', 'kajur', 'kjm', 'koordinator']))
                    <li><a href="{{ route('kriteria.show', 1) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 1 ? 'active' : '' }}">Kriteria 1</a></li>
                    <li><a href="{{ route('kriteria.show', 2) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 2 ? 'active' : '' }}">Kriteria 2</a></li>
                    <li><a href="{{ route('kriteria.show', 3) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 3 ? 'active' : '' }}">Kriteria 3</a></li>
                    <li><a href="{{ route('kriteria.show', 4) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 4 ? 'active' : '' }}">Kriteria 4</a></li>
                    <li><a href="{{ route('kriteria.show', 5) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 5 ? 'active' : '' }}">Kriteria 5</a></li>
                    <li><a href="{{ route('kriteria.show', 6) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 6 ? 'active' : '' }}">Kriteria 6</a></li>
                    <li><a href="{{ route('kriteria.show', 7) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 7 ? 'active' : '' }}">Kriteria 7</a></li>
                    <li><a href="{{ route('kriteria.show', 8) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 8 ? 'active' : '' }}">Kriteria 8</a></li>
                    <li><a href="{{ route('kriteria.show', 9) }}" class="{{ request()->routeIs('kriteria.show') && request()->route('kriteria') == 9 ? 'active' : '' }}">Kriteria 9</a></li>
                    @endif
                </ul>
            </li>

            {{-- Template Dokumen for non-admin roles --}}
            @if(auth()->user()->role !== 'administrator')
            <li>
                <a href="{{ route('templates.index') }}" class="{{ request()->routeIs('templates.*') ? 'active' : '' }}">
                    <div class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#90959f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </div>
                    <span class="nav-text">Template Dokumen</span>
                </a>
            </li>
            @endif

            <li>
                <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">
                    <div class="menu-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M9.13478 20.7733V17.7156C9.13478 16.9351 9.77217 16.3023 10.5584 16.3023H13.4326C13.8102 16.3023 14.1723 16.4512 14.4393 16.7163C14.7063 16.9813 14.8563 17.3408 14.8563 17.7156V20.7733C14.8539 21.0978 14.9821 21.4099 15.2124 21.6402C15.4427 21.8705 15.756 22 16.0829 22H18.0438C18.9596 22.0024 19.8388 21.6428 20.4872 21.0008C21.1356 20.3588 21.5 19.487 21.5 18.5778V9.86686C21.5 9.13246 21.1721 8.43584 20.6046 7.96467L13.934 2.67587C12.7737 1.74856 11.1111 1.7785 9.98539 2.74698L3.46701 7.96467C2.87274 8.42195 2.51755 9.12064 2.5 9.86686V18.5689C2.5 20.4639 4.04738 22 5.95617 22H7.87229C8.55123 22 9.103 21.4562 9.10792 20.7822L9.13478 20.7733Z"
                                fill="#90959F" />
                        </svg>
                    </div>
                    <span class="nav-text">Home</span>
                </a>
            </li>

        </ul>
    </div>
</div>
