<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                <!-- Tombol close (X) dihapus sesuai permintaan -->
            </div>
            <form id="addUserForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                        <span class="invalid-feedback" role="alert" id="nama-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        <span class="invalid-feedback" role="alert" id="username-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                        <span class="invalid-feedback" role="alert" id="email-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="invalid-feedback" role="alert" id="password-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="administrator">Administrator</option>
                            <option value="dosen">Dosen</option>
                            <option value="koordinator">Koordinator</option>
                            <option value="kjm">KJM</option>
                            <option value="kaprodi">Kaprodi</option>
                            <option value="kajur">Kajur</option>
                        </select>
                        <span class="invalid-feedback" role="alert" id="role-error"></span>
                    </div>

                    <div id="kriteria-access-container" style="display: none;">
                        <div class="form-group">
                            <label>Kriteria Access</label>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Kriteria access hanya berlaku untuk dosen. Role lain secara otomatis memiliki akses ke semua kriteria.
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-info mb-2" id="show-kriteria-selector">
                                <i class="fas fa-list"></i> Tampilkan Pilihan Kriteria
                            </button>
                            <select class="form-control select2" id="kriteria-access" name="kriteria_access[]" multiple>
                                @foreach(App\Models\Kriteria::all() as $kriteria)
                                <option value="{{ $kriteria->id }}">
                                    {{ $kriteria->nama_kriteria }}
                                </option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback" role="alert" id="kriteria_access-error"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
</div> 