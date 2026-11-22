// Portal Admin & Nasabah. DEMO front-end: data di localStorage; di Laravel semua aturan ini berjalan di service + DB transaction.
(()=>{'use strict';
const B=document.body.dataset,ROLE=B.role,PG=B.page,KEY='bs1',F={};
// BR-14 (demo): Admin awal wajib ganti kata sandi dulu. Di Laravel: middleware must_change_password.
if(ROLE=='admin'&&!(()=>{try{return sessionStorage.getItem('pwok')}catch(e){return 1}})()){location.replace('ganti-kata-sandi.html');return}
const E=s=>String(s).replace(/[&<>"']/g,c=>'&#'+c.charCodeAt(0)+';');
const dt=()=>new Date().toISOString(),today=()=>dt().slice(0,10),nowS=()=>dt().slice(0,16).replace('T',' ');
const rw=(j,kg,h)=>({j,kg,h,sub:Math.round(kg*h)});
function seed(){return{sn:191,tn:1,me:1,
jenis:[{id:1,n:'Kertas',a:1},{id:2,n:'Plastik',a:1},{id:3,n:'Logam',a:1}],
harga:[{j:1,h:2000,t:'2026-11-01'},{j:2,h:3000,t:'2026-11-01'},{j:3,h:5000,t:'2026-11-01'}],
ns:[{id:1,kode:'NSB-0842',nama:'Ani',email:'ani@sahabatibu.id',tel:'081234567890',alamat:'Perum. Taman Gading, Jember',st:'aktif',saldo:187500,tahan:0},
{id:2,kode:'NSB-0843',nama:'Sari',email:'sari@mail.id',tel:'081200000001',alamat:'Taman Gading Blok C',st:'aktif',saldo:15000,tahan:0},
{id:3,kode:'',nama:'Dewi',email:'dewi@mail.id',tel:'081200000002',alamat:'Kaliwates',st:'menunggu',saldo:0,tahan:0},
{id:4,kode:'NSB-0801',nama:'Rina',email:'rina@mail.id',tel:'081200000003',alamat:'Tegal Besar',st:'nonaktif',saldo:0,tahan:0}],
set:[{id:'SET-0190',n:2,t:'2026-11-18',rows:[rw(2,5,3000)],v:15000,st:'berhasil'},{id:'SET-0181',n:1,t:'2026-11-14',rows:[rw(1,8,2000)],v:16000,st:'berhasil'},
{id:'SET-0176',n:1,t:'2026-11-07',rows:[rw(3,1.8,5000)],v:9000,st:'berhasil'},{id:'SET-0120',n:1,t:'2026-10-20',rows:[rw(3,14,5000),rw(2,14,3000)],v:112000,st:'berhasil'},
{id:'SET-0099',n:1,t:'2026-10-03',rows:[rw(2,10,3000),rw(1,10.25,2000)],v:50500,st:'berhasil'}],tar:[],log:[]}}
let db;try{db=JSON.parse(localStorage.getItem(KEY))}catch(e){}db=db||seed();
const save=()=>{try{localStorage.setItem(KEY,JSON.stringify(db))}catch(e){}};
const nm=id=>db.ns.find(x=>x.id==id),me=()=>nm(db.me),av=n=>n.saldo-n.tahan,jn=id=>db.jenis.find(x=>x.id==id).n,pn=s=>parseFloat(String(s).replace(',','.'));
const sum=(a,f)=>a.reduce((x,y)=>x+f(y),0),okKg=v=>/^\d+([.,]\d{1,2})?$/.test(String(v).trim())&&pn(v)>0,TEL=/^[0-9+]{1,20}$/,EM=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const hNow=j=>(db.harga.filter(x=>x.j==j&&x.t<=today()).sort((a,b)=>b.t.localeCompare(a.t))[0]||{h:0}).h;
const lg=(a,k)=>db.log.unshift({t:nowS(),by:ROLE=='admin'?'Admin':me().nama,u:ROLE=='admin'?0:db.me,a,k,ip:'127.0.0.1'});
// ---- service (aturan bisnis PRD) ----
function setor(nid,rows){const n=nm(nid);if(!n||n.st!='aktif')throw Error('Nasabah harus berstatus Aktif (BR-01).');if(!rows.length)throw Error('Isi berat minimal satu jenis sampah.');
 const r=rows.map(x=>{if(!okKg(x.kg))throw Error('Berat harus > 0, maksimal 2 desimal (BR-05).');if(!hNow(x.j))throw Error('Harga jenis ini belum diatur.');return rw(x.j,+pn(x.kg).toFixed(2),hNow(x.j))});
 const v=sum(r,x=>x.sub),id='SET-0'+db.sn++;n.saldo+=v;db.set.unshift({id,n:nid,t:today(),rows:r,v,st:'berhasil'});lg('setoran_dibuat',id);save();return id}
function batalSetor(id,al){if(!al.trim())throw Error('Alasan wajib diisi.');const s=db.set.find(x=>x.id==id),n=nm(s.n);if(s.st!='berhasil')throw Error('Setoran sudah dibatalkan.');
 if(av(n)<s.v)throw Error('Ditolak: saldo tersedia nasabah kurang dari nilai setoran (BR-17).');n.saldo-=s.v;s.st='dibatalkan';s.al=al.trim();lg('setoran_dibatalkan',id);save()}
function ajukan(nom){const n=me();nom=+nom;if(n.st!='aktif')throw Error('Akun belum Aktif (BR-13).');if(!(nom>0))throw Error('Nominal harus lebih dari 0.');if(nom>av(n))throw Error('Nominal melebihi saldo tersedia (BR-07).');
 n.tahan+=nom;const id='TRK-'+String(db.tn++).padStart(4,'0');db.tar.unshift({id,n:n.id,v:nom,st:'Menunggu',t:nowS()});lg('penarikan_diajukan',id);save()}
function tr(id,ac,al){const t=db.tar.find(x=>x.id==id),n=nm(t.n),M={batal:['Menunggu','Dibatalkan'],tolak:['Menunggu','Ditolak'],setuju:['Menunggu','Disetujui'],cairkan:['Disetujui','Dicairkan']}[ac];
 if(!M||t.st!=M[0])throw Error('Transisi status tidak diizinkan (BR-08).');if(ac=='tolak'&&!(al||'').trim())throw Error('Alasan penolakan wajib diisi.');
 if(ac=='batal'||ac=='tolak')n.tahan-=t.v;if(ac=='cairkan'){n.saldo-=t.v;n.tahan-=t.v}t.st=M[1];if(al)t.al=al.trim();lg('penarikan_'+M[1].toLowerCase(),id);save()}
function ver(id,ok,al){const n=nm(id);if(n.st!='menunggu')throw Error('Bukan pendaftar menunggu.');if(ok){n.st='aktif';n.kode='NSB-0'+(840+id)}else{if(!(al||'').trim())throw Error('Alasan penolakan wajib diisi.');n.st='ditolak';n.al=al.trim()}lg(ok?'pendaftar_disetujui':'pendaftar_ditolak',n.email);save()}
function anon(id){const n=nm(id);if(n.st!='nonaktif')throw Error('Hanya akun Nonaktif yang bisa dianonimkan.');
 if(n.saldo!=0||db.tar.some(t=>t.n==id&&['Menunggu','Disetujui'].includes(t.st)))throw Error('Ditolak: saldo belum nol atau ada penarikan berjalan (BR-18).');
 Object.assign(n,{nama:'Anonim-'+id,email:'anonim-'+id+'@anonim.invalid',tel:'-',alamat:'-',anon:1});db.log.forEach(l=>{if(l.u==id)l.ip=''});lg('data_dianonimkan','nasabah #'+id);save()}
// ---- tampilan ----
const kpi=a=>'<div class="kpi">'+a.map(x=>`<div class="g"><small>${x[0]}</small><b>${x[1]}</b></div>`).join('')+'</div>';
const tb=(h,r,id)=>`<div class="tbl"><table${id?` id="${id}"`:''}><thead><tr>${h.map(x=>'<th>'+x+'</th>').join('')}</tr></thead><tbody>${r.length?r.map(x=>'<tr>'+x.map(c=>'<td>'+c+'</td>').join('')+'</tr>').join(''):`<tr><td colspan="${h.length}">Belum ada data.</td></tr>`}</tbody></table></div>`;
const bt=(l,d,id,c)=>`<button class="btn ${c||'o'} sm" data-do="${d}" data-id="${E(id)}">${l}</button>`;
const pl=s=>`<span class="pill ${/tolak|batal|nonaktif/i.test(s)?'no':/aktif|berhasil|cair|setuju/i.test(s)?'ok':''}">${E(s)}</span>`;
const fl=(l,n,t,x)=>`<label>${l}<input name="${n}" type="${t||'text'}" ${x||''}></label>`;
const sel=(n,o,v,all)=>`<label>${n[1]}<select name="${n[0]}">${all?'<option value="">Semua</option>':''}${o.map(x=>`<option value="${x[0]}"${v==x[0]?' selected':''}>${E(x[1])}</option>`).join('')}</select></label>`;
const det=s=>s.rows.map(r=>E(jn(r.j))+' '+fmt(r.kg)+' kg').join(', '),inR=t=>(!F.d||t>=F.d)&&(!F.s||t<=F.s);
const head=(k,t)=>`<small class="kick">${k}</small><h1>${t}</h1>`;
const filt=()=>`<form class="f frow" data-do="filt">${fl('Dari','d','date','value="'+(F.d||'')+'"')}${fl('Sampai','s','date','value="'+(F.s||'')+'"')}<button class="btn sm">Terapkan</button></form>`;
const mine=()=>db.set.filter(s=>s.n==db.me),ak=()=>db.ns.filter(n=>n.st=='aktif');
const P={
'nasabah-dashboard'(){const n=me(),s=mine().filter(x=>x.st=='berhasil'),t=db.tar.find(x=>x.n==n.id);
 return head('Dashboard','Halo, <em>'+E(n.nama)+'</em>')+kpi([['Uang bisa diambil',rp(av(n))],['Sedang ditahan',rp(n.tahan)],['Total sampah',fmt(sum(s,x=>sum(x.rows,r=>r.kg)))+' kg'],['Total setoran',rp(sum(s,x=>x.v))]])+'<h2>Setoran terbaru</h2>'+tb(['Kode','Tanggal','Rincian','Nilai'],s.slice(0,3).map(x=>[x.id,x.t,det(x),rp(x.v)]))+'<h2>Penarikan terakhir</h2><p class="lead">'+(t?E(t.id)+' · '+rp(t.v)+' · '+pl(t.st):'Belum ada pengajuan.')+'</p>'},
'nasabah-riwayat'(){return head('Riwayat','Riwayat <em>setoran</em>')+filt()+tb(['Kode','Tanggal','Rincian','Nilai','Status'],mine().filter(x=>inR(x.t)).map(x=>[x.id,x.t,det(x),rp(x.v),pl(x.st)]))},
'nasabah-penarikan'(){const n=me();return head('Penarikan dana','Ajukan <em>ambil uang</em>')+`<p class="lead">Tersedia <b>${rp(av(n))}</b> · ditahan ${rp(n.tahan)}</p><form class="f frow" data-do="ajukan">${fl('Nominal (Rp)','nom','number','min="1" step="1000" required')}<button class="btn sm">Ajukan</button></form>`+tb(['Kode','Waktu','Nominal','Status',''],db.tar.filter(t=>t.n==n.id).map(t=>[t.id,t.t,rp(t.v),pl(t.st)+(t.al?' · '+E(t.al):''),t.st=='Menunggu'?bt('Batalkan','batal',t.id):'']))},
'nasabah-katalog'(){return head('Katalog','Harga sampah <em>hari ini</em>')+tb(['Jenis','Harga berlaku'],db.jenis.filter(j=>j.a).map(j=>[E(j.n),rp(hNow(j.id))+'/kg']))},
'nasabah-profil'(){const n=me();return head('Profil','Data <em>Ibu</em>')+`<p class="lead">${E(n.nama)} · ${E(n.kode)} · ${E(n.email)}</p><form class="f" data-do="prof">${fl('Telepon','tel','tel','maxlength="20" required value="'+E(n.tel)+'"')}${fl('Alamat','alamat','text','maxlength="255" required value="'+E(n.alamat)+'"')}<button class="btn">Simpan</button></form><p class="sub"><a class="lk" href="kebijakan-privasi.html">Kebijakan Privasi</a></p>`},
'admin-dashboard'(){const s=db.set.filter(x=>x.st=='berhasil'),bad=db.ns.filter(n=>n.saldo!=sum(s.filter(x=>x.n==n.id),x=>x.v)-sum(db.tar.filter(t=>t.n==n.id&&t.st=='Dicairkan'),t=>t.v)||n.tahan!=sum(db.tar.filter(t=>t.n==n.id&&/Menunggu|Disetujui/.test(t.st)),t=>t.v));
 return head('Dashboard','Ringkasan <em>bank sampah</em>')+kpi([['Nasabah aktif',ak().length],['Total setoran',rp(sum(s,x=>x.v))],['Penarikan menunggu',db.tar.filter(t=>t.st=='Menunggu').length],['Pendaftar menunggu',db.ns.filter(n=>n.st=='menunggu').length]])+'<h2>Total sampah per jenis</h2>'+tb(['Jenis','Berat'],db.jenis.map(j=>[E(j.n),fmt(sum(s,x=>sum(x.rows.filter(r=>r.j==j.id),r=>r.kg)))+' kg']))+'<h2>Audit saldo</h2><p class="lead">'+(bad.length?'Selisih pada '+bad.length+' akun.':'Saldo = Σ setoran berhasil − Σ penarikan dicairkan, ditahan = Σ pengajuan berjalan: sesuai (NF-09).')+'</p>'},
'admin-pendaftar'(){const w=db.ns.filter(n=>n.st=='menunggu');return head('Pendaftar & nasabah','Verifikasi <em>pendaftar</em>')+tb(['Nama','Email','Telepon','Alamat',''],w.map(n=>[E(n.nama),E(n.email),E(n.tel),E(n.alamat),bt('Setujui','ver',n.id,'')+' '+bt('Tolak','vtolak',n.id)]))
 +`<h2>Tambah nasabah (langsung Aktif)</h2><form class="f" data-do="tambah">${fl('Nama','nama','text','maxlength="100" required')}${fl('Email','email','email','maxlength="255" required')}${fl('Telepon','tel','tel','maxlength="20" required')}${fl('Alamat','alamat','text','maxlength="255" required')}<button class="btn">Tambah</button></form><h2>Daftar nasabah</h2><input id="q" class="srch" placeholder="Cari nama atau nomor" aria-label="Cari">`
 +tb(['No.','Nama','Email','Status','Saldo',''],db.ns.filter(n=>n.st!='menunggu').map(n=>[E(n.kode),E(n.nama),E(n.email),pl(n.st),rp(n.saldo),(n.anon?'':bt('Ubah','edit',n.id)+' ')+(n.st=='aktif'?bt('Nonaktifkan','tog',n.id):'')+(n.st=='nonaktif'?bt('Aktifkan','tog',n.id)+(n.anon?'':' '+bt('Anonimkan','anon',n.id)):'')]),'tn')},
'admin-katalog'(){return head('Katalog harga','Harga sampah <em>harian</em>')+`<form class="f frow" data-do="harga">${sel(['j','Jenis'],db.jenis.map(j=>[j.id,j.n]))}${fl('Harga/kg (Rp)','h','number','min="1" step="100" required')}${fl('Berlaku mulai','t','date','required value="'+today()+'"')}<button class="btn sm">Simpan</button></form>`+tb(['Jenis','Harga berlaku'],db.jenis.map(j=>[E(j.n),rp(hNow(j.id))+'/kg']))+'<h2>Riwayat harga</h2>'+tb(['Jenis','Harga/kg','Berlaku mulai'],db.harga.map(x=>[E(jn(x.j)),rp(x.h),x.t]))},
'admin-setoran'(){return head('Setoran','Catat <em>setoran</em>')+`<form class="f" data-do="setor">${sel(['n','Nasabah'],ak().map(n=>[n.id,n.kode+' · '+n.nama]))}<div class="frow">${db.jenis.filter(j=>j.a).map(j=>fl(E(j.n)+' (kg) @ '+rp(hNow(j.id)),'kg'+j.id,'text','inputmode="decimal" placeholder="0,00"')).join('')}</div><p class="lead" id="rk">Total: Rp 0</p><button class="btn">Simpan setoran</button></form><h2>Transaksi</h2><form class="f frow" data-do="fn">${sel(['n','Nasabah'],db.ns.filter(n=>n.kode).map(n=>[n.id,n.nama]),F.n,1)}<button class="btn sm">Filter</button></form>`
 +tb(['Kode','Tanggal','Nasabah','Rincian','Nilai','Status',''],db.set.filter(x=>!F.n||x.n==F.n).map(x=>[x.id,x.t,E(nm(x.n).nama),det(x),rp(x.v),pl(x.st),x.st=='berhasil'?bt('Batalkan','bs',x.id):E(x.al||'')]))},
'admin-penarikan'(){return head('Penarikan','Pengajuan <em>penarikan</em>')+tb(['Kode','Waktu','Nasabah','Nominal','Status',''],db.tar.map(t=>[t.id,t.t,E(nm(t.n).nama),rp(t.v),pl(t.st)+(t.al?' · '+E(t.al):''),t.st=='Menunggu'?bt('Setujui','setuju',t.id,'')+' '+bt('Tolak','tolak',t.id):t.st=='Disetujui'?bt('Cairkan (tunai diserahkan)','cairkan',t.id,''):'']))},
'admin-laporan'(){const s=db.set.filter(x=>x.st=='berhasil'&&inR(x.t)),c=db.tar.filter(t=>t.st=='Dicairkan'&&inR(t.t.slice(0,10)));
 return head('Laporan','Rekap <em>periode</em>')+filt()+kpi([['Total setoran',rp(sum(s,x=>x.v))],['Transaksi',s.length],['Dicairkan',rp(sum(c,t=>t.v))]])+'<div class="row noprint">'+bt('Ekspor CSV','csv','','')+' '+bt('Cetak','print','')+'</div><h2>Per jenis</h2>'+tb(['Jenis','Berat','Nilai'],db.jenis.map(j=>{const r=s.flatMap(x=>x.rows.filter(y=>y.j==j.id));return[E(j.n),fmt(sum(r,y=>y.kg))+' kg',rp(sum(r,y=>y.sub))]}))+'<h2>Per nasabah</h2>'+tb(['No.','Nama','Nilai'],db.ns.filter(n=>n.kode).map(n=>[E(n.kode),E(n.nama),rp(sum(s.filter(x=>x.n==n.id),x=>x.v))]))},
'admin-profil'(){return head('Profil admin','Akun & <em>log aktivitas</em>')+`<form class="f" data-do="sandi">${fl('Kata sandi lama','a','password','autocomplete="current-password" required')}${fl('Kata sandi baru','b','password','autocomplete="new-password" required')}${fl('Ulangi kata sandi baru','c','password','autocomplete="new-password" required')}<button class="btn">Ganti kata sandi</button></form><h2>Log aktivitas</h2>`+tb(['Waktu','Pelaku','Aksi','Keterangan','IP'],db.log.slice(0,30).map(l=>[l.t,E(l.by),E(l.a),E(l.k),E(l.ip)]))},
'admin-jenis'(){const ada=db.jenis.map(j=>j.n),sisa=['Kertas','Plastik','Logam'].filter(n=>!ada.includes(n));
 return head('Jenis sampah','Kelola <em>jenis sampah</em>')+'<p class="lead">Jenis hanya kertas, botol plastik, dan logam (BR-02). Harga dihitung per kg (BR-03).</p>'+db.jenis.map(j=>`<form id="fj${j.id}" data-do="jsimpan" data-id="${j.id}"></form>`).join('')
 +tb(['Nama','Satuan','Harga','Status',''],db.jenis.map(j=>[`<input class="srch" form="fj${j.id}" name="n" maxlength="50" value="${E(j.n)}" aria-label="Nama">`,`<input class="srch" form="fj${j.id}" name="u" maxlength="10" value="${E(j.u||'kg')}" aria-label="Satuan">`,rp(hNow(j.id)),pl(j.a?'Aktif':'Nonaktif'),`<button class="btn sm" form="fj${j.id}">Simpan</button> `+bt(j.a?'Nonaktifkan':'Aktifkan','jtog',j.id)]))
 +(sisa.length?`<h2>Tambah jenis</h2><form class="f frow" data-do="jtambah">${sel(['n','Jenis'],sisa.map(v=>[v,v]))}${fl('Satuan','u','text','maxlength="10" value="kg" required')}${fl('Harga awal/kg (Rp)','h','number','min="1" step="100" required')}<button class="btn sm">Tambah</button></form>`:'')},
'admin-nasabah-ubah'(){const n=nm(new URLSearchParams(location.search).get('id')),back='<p class="sub"><a class="lk" href="admin-pendaftar.html">Kembali ke daftar nasabah</a></p>';
 if(!n||n.anon)return head('Ubah nasabah','Data <em>tidak tersedia</em>')+back;
 return head('Ubah nasabah','Ubah data <em>'+E(n.nama)+'</em>')+`<p class="lead">${E(n.kode)} · saldo dan transaksi tidak ikut berubah.</p><form class="f" data-do="nsimpan" data-id="${n.id}">${fl('Nama','nama','text','maxlength="100" required value="'+E(n.nama)+'"')}${fl('Email','email','email','maxlength="255" required value="'+E(n.email)+'"')}${fl('Telepon','tel','tel','maxlength="20" required value="'+E(n.tel)+'"')}${fl('Alamat','alamat','text','maxlength="255" required value="'+E(n.alamat)+'"')}<button class="btn">Simpan</button></form>`+back},
};
// ---- aksi ----
const ask=q=>new Promise(r=>{const d=document.createElement('dialog');d.innerHTML=`<form method="dialog" class="f"><h3>${E(q)}</h3><label>Alasan<input name="a" maxlength="255" required></label><div class="row"><button class="btn" value="ok">Kirim</button><button class="btn o" value="x" formnovalidate>Batal</button></div></form>`;document.body.append(d);d.addEventListener('close',()=>{r(d.returnValue=='ok'?d.querySelector('input').value:null);d.remove()});d.showModal()});
const q=c=>{c=String(c);if(/^[=+\-@]/.test(c))c="'"+c;return'"'+c.replace(/"/g,'""')+'"'}; // netralkan injeksi rumus (F-33)
const DO={
menu(){$('side').classList.toggle('open');$('scrim').classList.toggle('on')},go(x){location.href=x.dataset.id+'.html'},out(){try{sessionStorage.removeItem('pwok')}catch(e){}location.href='login.html'},
edit(x){location.href='admin-nasabah-ubah.html?id='+encodeURIComponent(x.dataset.id)},
jsimpan(f){const j=db.jenis.find(y=>y.id==f.dataset.id),n=f.n.value.trim(),u=f.u.value.trim();if(!n||!u)throw Error('Nama dan satuan wajib diisi.');if(!['Kertas','Plastik','Logam'].includes(n))throw Error('Jenis hanya kertas, botol plastik, logam (BR-02).');if(db.jenis.some(y=>y.id!=j.id&&y.n==n))throw Error('Jenis sudah ada.');j.n=n;j.u=u;lg('jenis_diubah',n);save();return'Jenis disimpan.'},
jtog(x){const j=db.jenis.find(y=>y.id==x.dataset.id);if(j.a&&db.jenis.filter(y=>y.a).length<2)throw Error('Minimal satu jenis harus aktif.');j.a=j.a?0:1;lg(j.a?'jenis_diaktifkan':'jenis_dinonaktifkan',j.n);save();return'Status jenis diperbarui.'},
jtambah(f){const h=+f.h.value,u=f.u.value.trim();if(!(h>0)||!u)throw Error('Satuan dan harga awal wajib (harga > 0).');const id=Math.max(...db.jenis.map(j=>j.id))+1;db.jenis.push({id,n:f.n.value,u,a:1});db.harga.unshift({j:id,h,t:today()});lg('jenis_ditambah',f.n.value);save();return'Jenis ditambahkan.'},
nsimpan(f){const n=nm(f.dataset.id),em=f.email.value.trim().toLowerCase();if(n.anon)throw Error('Data sudah dianonimkan.');if(!f.nama.value.trim()||!EM.test(em)||!TEL.test(f.tel.value)||!f.alamat.value.trim())throw Error('Periksa isian nama, email, telepon, alamat.');if(db.ns.some(y=>y.id!=n.id&&y.email==em))throw Error('Email sudah dipakai (BR-15).');Object.assign(n,{nama:f.nama.value.trim(),email:em,tel:f.tel.value,alamat:f.alamat.value.trim()});lg('nasabah_diubah',n.kode);save();return'Data nasabah diperbarui.'},
filt(f){F.d=f.d.value;F.s=f.s.value},fn(f){F.n=f.n.value},
ajukan(f){ajukan(f.nom.value);return'Pengajuan masuk, dana ditahan.'},batal(x){tr(x.dataset.id,'batal');return'Pengajuan dibatalkan, dana kembali.'},
setuju(x){tr(x.dataset.id,'setuju');return'Pengajuan disetujui.'},cairkan(x){tr(x.dataset.id,'cairkan');return'Dicairkan dan tercatat.'},
async tolak(x){const a=await ask('Alasan penolakan');if(a===null)return;tr(x.dataset.id,'tolak',a);return'Pengajuan ditolak.'},
prof(f){const n=me();if(!TEL.test(f.tel.value)||!f.alamat.value.trim())throw Error('Telepon (angka/+) dan alamat wajib diisi.');n.tel=f.tel.value;n.alamat=f.alamat.value.trim();lg('profil_diubah',n.kode);save();return'Profil diperbarui.'},
setor(f){const r=db.jenis.filter(j=>j.a&&f['kg'+j.id].value.trim()).map(j=>({j:j.id,kg:f['kg'+j.id].value}));return'Tersimpan: '+setor(+f.n.value,r)},
async bs(x){const a=await ask('Alasan pembatalan setoran');if(a===null)return;batalSetor(x.dataset.id,a);return'Setoran dibatalkan, saldo dikurangi.'},
ver(x){ver(+x.dataset.id,1);return'Pendaftar disetujui (Aktif).'},
async vtolak(x){const a=await ask('Alasan penolakan');if(a===null)return;ver(+x.dataset.id,0,a);return'Pendaftar ditolak.'},
tambah(f){const em=f.email.value.trim().toLowerCase();if(!f.nama.value.trim()||!EM.test(em)||!TEL.test(f.tel.value)||!f.alamat.value.trim())throw Error('Periksa isian nama, email, telepon, alamat.');if(db.ns.some(n=>n.email==em))throw Error('Email sudah terdaftar (BR-15).');
 const id=Math.max(...db.ns.map(n=>n.id))+1;db.ns.push({id,kode:'NSB-0'+(840+id),nama:f.nama.value.trim(),email:em,tel:f.tel.value,alamat:f.alamat.value.trim(),st:'aktif',saldo:0,tahan:0});lg('nasabah_ditambah',em);save();return'Nasabah ditambahkan.'},
tog(x){const n=nm(x.dataset.id);n.st=n.st=='aktif'?'nonaktif':'aktif';lg('status_'+n.st,n.kode);save();return'Status: '+n.st},
async anon(x){if(!confirm('Anonimkan data nasabah ini?')||!confirm('Yakin? Tidak bisa dibatalkan.'))return;anon(+x.dataset.id);return'Data dianonimkan.'},
harga(f){const h=+f.h.value;if(!(h>0))throw Error('Harga harus lebih dari 0.');db.harga.unshift({j:+f.j.value,h,t:f.t.value||today()});lg('harga_diubah',jn(+f.j.value)+' '+h);save();return'Harga disimpan; transaksi lama tidak berubah (BR-04).'},
csv(){const s=db.set.filter(x=>x.st=='berhasil'&&inR(x.t)),l=[['Nomor nasabah','Nama','Nilai transaksi'],...s.map(x=>[nm(x.n).kode,nm(x.n).nama,x.v])].map(r=>r.map(q).join(',')).join('\r\n'),a=document.createElement('a');
 a.href=URL.createObjectURL(new Blob(['\ufeff'+l],{type:'text/csv'}));a.download='laporan-setoran.csv';a.click();setTimeout(()=>URL.revokeObjectURL(a.href),1e3)},print(){window.print()},
sandi(f){if(!/^(?=.*[A-Za-z])(?=.*\d).{8,}$/.test(f.b.value))throw Error('Kata sandi baru: min. 8 karakter, huruf dan angka.');if(f.b.value!==f.c.value)throw Error('Konfirmasi tidak sama.');f.reset();return'Kata sandi diganti (di Laravel: di-hash di server).'}};
const run=(fn,x)=>Promise.resolve().then(()=>fn(x)).then(m=>{render();if(m)toast(m)}).catch(e=>toast(e.message));
// ---- kerangka halaman ----
const NAV=ROLE=='admin'?[['admin-dashboard','Dashboard'],['admin-pendaftar','Pendaftar & Nasabah'],['admin-katalog','Katalog Harga'],['admin-jenis','Jenis Sampah'],['admin-setoran','Setoran'],['admin-penarikan','Penarikan'],['admin-laporan','Laporan'],['admin-profil','Profil & Log']]:[['nasabah-dashboard','Dashboard'],['nasabah-riwayat','Riwayat Setoran'],['nasabah-penarikan','Penarikan Dana'],['nasabah-katalog','Katalog Harga'],['nasabah-profil','Profil']];
$('app').innerHTML=`<header id="top"><div class="crumb g"><button class="burger" data-do="menu" aria-label="Buka menu">☰</button><div><b>${E((NAV.find(x=>x[0]==PG)||[0,'Ubah Nasabah'])[1])}</b><small>${ROLE=='admin'?'Admin':'Nasabah'} · Sahabat Ibu</small></div></div><div class="right"><div class="bal g"><small id="bl"></small><b id="bv"></b></div></div></header>
<aside id="side" class="g" aria-label="Menu utama"><a class="brand" href="index.html"><i></i><div><b>Sahabat Ibu</b><span>Bank Sampah, Jember</span></div></a><nav id="keys">${NAV.map(x=>`<button data-do="go" data-id="${x[0]}"${x[0]==PG?' class="on" aria-current="page"':''}><span class="t">${x[1]}</span></button>`).join('')}<small class="gl">Lainnya</small><button data-do="go" data-id="index"><span class="t">Garasi 3D</span></button><button data-do="out"><span class="t">Keluar</span></button></nav></aside><div id="scrim"></div><div id="toast" role="status" aria-live="polite"></div><main class="pgm top"><section class="box x g" id="isi"></section></main>`;
$('scrim').onclick=DO.menu;
function render(){$('isi').innerHTML=P[PG]();$('bl').textContent=ROLE=='admin'?'Pengajuan menunggu':'Uang bisa diambil';$('bv').textContent=ROLE=='admin'?db.tar.filter(t=>t.st=='Menunggu').length:rp(av(me()))}
document.addEventListener('click',e=>{const b=e.target.closest('button[data-do]');if(b){e.preventDefault();run(DO[b.dataset.do],b)}});
document.addEventListener('submit',e=>{const f=e.target.closest('form[data-do]');if(f){e.preventDefault();run(DO[f.dataset.do],f)}});
document.addEventListener('input',e=>{const t=e.target;if(t.id=='q'){const v=t.value.toLowerCase();document.querySelectorAll('#tn tbody tr').forEach(r=>{r.hidden=!r.textContent.toLowerCase().includes(v)})}
 else if(/^kg\d/.test(t.name||'')){let s=0;db.jenis.forEach(j=>{const i=t.form['kg'+j.id];if(i&&okKg(i.value))s+=Math.round(pn(i.value)*hNow(j.id))});$('rk').textContent='Total: '+rp(s)}});
render();
})();
