<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
              <!-- Tombol X dihapus, hanya tombol close teks yang berfungsi -->
          </div>
          <form id="editUserForm">
              @csrf
              @method('PUT')
              <input type="hidden" id="edit-id" name="id">
              <div class="modal-body">
                  <div class="form-group">
                      <label for="edit-nama">Nama</label>
                      <input type="text" class="form-control" id="edit-nama" name="nama" required>
                      <span class="invalid-feedback" role="alert" id="edit-nama-error"></span>
                  </div>
                  <div class="form-group">
                      <label for="edit-username">Username</label>
                      <input type="text" class="form-control" id="edit-username" name="username" required>
                      <span class="invalid-feedback" role="alert" id="edit-username-error"></span>
                  </div>
                  <div class="form-group">
                      <label for="edit-email">Email</label>
                      <input type="email" class="form-control" id="edit-email" name="email">
                      <span class="invalid-feedback" role="alert" id="edit-email-error"></span>
                  </div>
                  <div class="form-group">
                      <label for="edit-password">Password (Kosongkan jika tidak ingin diubah)</label>
                      <input type="password" class="form-control" id="edit-password" name="password">
                      <span class="invalid-feedback" role="alert" id="edit-password-error"></span>
                  </div>
                  <div class="form-group">
                      <label for="edit-role">Role</label>
                      <select class="form-control" id="edit-role" name="role" required>
                          <option value="">Select Role</option>
                          <option value="administrator">Administrator</option>
                          <option value="dosen">Dosen</option>
                          <option value="koordinator">Koordinator</option>
                          <option value="kjm">KJM</option>
                          <option value="kaprodi">Kaprodi</option>
                          <option value="kajur">Kajur</option>
                      </select>
                      <span class="invalid-feedback" role="alert" id="edit-role-error"></span>
                  </div>

                  <div id="edit-kriteria-access-container" style="display: none;">
                      <div class="form-group">
                          <label>Kriteria Access</label>
                          <div class="alert alert-info">
                              <i class="fas fa-info-circle"></i> Kriteria access hanya berlaku untuk dosen. Role lain secara otomatis memiliki akses ke semua kriteria.
                          </div>
                          <button type="button" class="btn btn-sm btn-outline-info mb-2" id="edit-show-kriteria-selector">
                              <i class="fas fa-list"></i> Tampilkan Pilihan Kriteria
                          </button>
                          <select class="form-control select2" id="edit-kriteria-access" name="kriteria_access[]" multiple>
                              @foreach(App\Models\Kriteria::all() as $kriteria)
                              <option value="{{ $kriteria->id }}">
                                  {{ $kriteria->nama_kriteria }}
                              </option>
                              @endforeach
                          </select>
                          <span class="invalid-feedback" role="alert" id="edit-kriteria_access-error"></span>
                      </div>
                  </div>
              </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                  <button type="submit" class="btn btn-primary">Update User</button>
              </div>
          </form>
      </div>
  </div>
</div> 