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
                <a href="{{ route('admin.history.index') }}" class="{{ request()->routeIs('admin.history.*') ? 'active' : '' }}">
                    <div class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#90959f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                        </svg>
                    </div>
                    <span class="nav-text">Log Aktivitas</span>
                </a>
            </li>
            @endif

            {{-- Kriteria Menu - Available for all roles --}}
            <li>
                <a class="has-arrow {{ request()->routeIs('kriteria.*') ? 'active' : '' }}" href="javascript:void(0);" aria-expanded="{{ request()->routeIs('kriteria.*') ? 'true' : 'false' }}">
                    <div class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="#90959f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                            <line x1="3" y1="6" x2="3" y2="6"></line>
                            <line x1="3" y1="12" x2="3" y2="12"></line>
                            <line x1="3" y1="18" x2="3" y2="18"></line>
                        </svg>
                    </div>
                    <span class="nav-text">Kriteria</span>
                </a>
                <ul aria-expanded="{{ request()->routeIs('kriteria.*') ? 'true' : 'false' }}">
                    @if(auth()->user()->role === 'administrator')
                    {{-- Admin sees all kriteria --}}
                    @php
                        $currentKriteriaId = null;
                        if (request()->routeIs('kriteria.show')) {
                            $currentKriteriaId = request()->route('kriteria');
                        } elseif (request()->routeIs('kriteria.upload.form')) {
                            $currentKriteriaId = request()->route('kriteria');
                        }
                    @endphp
                    <li><a href="{{ route('kriteria.show', 1) }}" class="{{ $currentKriteriaId == 1 ? 'active' : '' }}">Kriteria 1</a></li>
                    <li><a href="{{ route('kriteria.show', 2) }}" class="{{ $currentKriteriaId == 2 ? 'active' : '' }}">Kriteria 2</a></li>
                    <li><a href="{{ route('kriteria.show', 3) }}" class="{{ $currentKriteriaId == 3 ? 'active' : '' }}">Kriteria 3</a></li>
                    <li><a href="{{ route('kriteria.show', 4) }}" class="{{ $currentKriteriaId == 4 ? 'active' : '' }}">Kriteria 4</a></li>
                    <li><a href="{{ route('kriteria.show', 5) }}" class="{{ $currentKriteriaId == 5 ? 'active' : '' }}">Kriteria 5</a></li>
                    <li><a href="{{ route('kriteria.show', 6) }}" class="{{ $currentKriteriaId == 6 ? 'active' : '' }}">Kriteria 6</a></li>
                    <li><a href="{{ route('kriteria.show', 7) }}" class="{{ $currentKriteriaId == 7 ? 'active' : '' }}">Kriteria 7</a></li>
                    <li><a href="{{ route('kriteria.show', 8) }}" class="{{ $currentKriteriaId == 8 ? 'active' : '' }}">Kriteria 8</a></li>
                    <li><a href="{{ route('kriteria.show', 9) }}" class="{{ $currentKriteriaId == 9 ? 'active' : '' }}">Kriteria 9</a></li>
                    @endif

                    {{-- Dosen Menu Items - Based on kriteria_access --}}
                    @if(auth()->user()->role === 'dosen' && !empty(auth()->user()->kriteria_access))
                        @php
                            $currentKriteriaId = null;
                            if (request()->routeIs('kriteria.show')) {
                                $currentKriteriaId = request()->route('kriteria');
                            } elseif (request()->routeIs('kriteria.upload.form')) {
                                $currentKriteriaId = request()->route('kriteria');
                            }
                        @endphp
                        @foreach(auth()->user()->kriteria_access as $kriteriaId)
                            <li><a href="{{ route('kriteria.show', $kriteriaId) }}" class="{{ $currentKriteriaId == $kriteriaId ? 'active' : '' }}">Kriteria {{ $kriteriaId }}</a></li>
                        @endforeach
                    @endif

                    {{-- Kaprodi, Kajur, KJM, Koordinator see all criteria --}}
                    @if(in_array(auth()->user()->role, ['kaprodi', 'kajur', 'kjm', 'koordinator', 'direktur']))
                    @php
                        $currentKriteriaId = null;
                        if (request()->routeIs('kriteria.show')) {
                            $currentKriteriaId = request()->route('kriteria');
                        } elseif (request()->routeIs('kriteria.upload.form')) {
                            $currentKriteriaId = request()->route('kriteria');
                        }
                    @endphp
                    <li><a href="{{ route('kriteria.show', 1) }}" class="{{ $currentKriteriaId == 1 ? 'active' : '' }}">Kriteria 1</a></li>
                    <li><a href="{{ route('kriteria.show', 2) }}" class="{{ $currentKriteriaId == 2 ? 'active' : '' }}">Kriteria 2</a></li>
                    <li><a href="{{ route('kriteria.show', 3) }}" class="{{ $currentKriteriaId == 3 ? 'active' : '' }}">Kriteria 3</a></li>
                    <li><a href="{{ route('kriteria.show', 4) }}" class="{{ $currentKriteriaId == 4 ? 'active' : '' }}">Kriteria 4</a></li>
                    <li><a href="{{ route('kriteria.show', 5) }}" class="{{ $currentKriteriaId == 5 ? 'active' : '' }}">Kriteria 5</a></li>
                    <li><a href="{{ route('kriteria.show', 6) }}" class="{{ $currentKriteriaId == 6 ? 'active' : '' }}">Kriteria 6</a></li>
                    <li><a href="{{ route('kriteria.show', 7) }}" class="{{ $currentKriteriaId == 7 ? 'active' : '' }}">Kriteria 7</a></li>
                    <li><a href="{{ route('kriteria.show', 8) }}" class="{{ $currentKriteriaId == 8 ? 'active' : '' }}">Kriteria 8</a></li>
                    <li><a href="{{ route('kriteria.show', 9) }}" class="{{ $currentKriteriaId == 9 ? 'active' : '' }}">Kriteria 9</a></li>
                    @endif
                </ul>
            </li>

            {{-- Dokumen Menu Item - Available for all roles
            <li>
                <a href="{{ route('dokumen.index') }}" class="{{ request()->routeIs('dokumen.*') ? 'active' : '' }}">
                    <div class="menu-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#90959f" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file">
                            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
                            <polyline points="13 2 13 9 20 9"></polyline>
                        </svg>
                    </div>
                    <span class="nav-text">Dokumen</span>
                </a>
            </li> --}}

            {{-- Template Dokumen for admin and dosen roles --}}
            @if (in_array(auth()->user()->role, ['administrator', 'dosen']))
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
