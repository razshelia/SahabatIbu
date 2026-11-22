// 1 PEMINDAI SALDO
const pm=new THREE.Group();pm.position.set(8.5,0,9.6);sc.add(pm);
box(2.1,1.1,1.3,cream,0,.55,0,pm);box(2.0,1.2,.14,M(0x26332d,.5),0,1.8,-.25,pm).rotation.x=-.3;
const pmS=screen(1.84,1.04,0,1.8,-.15,pm);pmS.rotation.x=-.3;
const beam=box(1.7,.03,.5,new THREE.MeshBasicMaterial({color:0x6fe0a2}),0,1.12,.3,pm);
screen(1.9,.32,0,.4,.66,pm).paint((g,W,H)=>{bg(g,W,H,'#f1ead8');tx(g,'CEK SALDO',W/2,H*.72,44,'#26332d','center')});
pmS.paint((g,W,H,B)=>{bg(g,W,H);tx(g,'Uang bisa diambil',24,48,28,'#9fd9b8','left','700');tx(g,rp(avail()),24,124,66);tx(g,'Sedang diajukan '+rp(S.tahan),24,164,26,'#f3c64f','left','700');
 B(24,H-92,280,70,'Cek saldo saya',()=>{boost=70;S.m.scan='Kartu terbaca';toast('Saldo Ibu: '+rp(avail())+' (bisa diambil)');redraw()},'g');tx(g,S.m.scan||'Ani · NSB-0842',W-20,H-42,22,'#9fd9b8','right','700')});
handOn(pmS,164,232,'scan',1);lab(pm,0,2.95,0,'1','Cek Saldo',2.5,'scan');ring(pm,1.6,'scan');

// 2 TIMBANGAN
const tm=new THREE.Group();tm.position.set(5.5,0,4.8);sc.add(tm);
box(2.7,.35,2.2,cream,0,.18,0,tm);cyl(.9,.12,M(0xb4bcc4,.3,.8),0,.46,.1,tm,40);
box(2.2,2.3,.5,cream,0,1.45,-.8,tm);const tmS=screen(1.9,1.55,0,1.5,-.54,tm);
screen(2.5,.28,0,.2,1.12,tm).paint((g,W,H)=>{bg(g,W,H,'#f1ead8');tx(g,'TIMBANG SAMPAH',W/2,H*.72,44,'#26332d','center')});
tmS.paint((g,W,H,B)=>{bg(g,W,H);tx(g,'Setor sampah',24,46,30,'#9fd9b8','left','700');tx(g,'Ani',W-24,46,28,'#fff','right');
 tx(g,fmt(wShow)+' kg',24,142,82);if(S.m.tm)tx(g,S.m.tm,24,182,22,'#f3c64f','left','700');
 const tot=S.cart.reduce((a,r)=>a+r.kg*J[r.j].h,0);
 S.cart.slice(0,2).forEach((r,i)=>{const y=216+i*34;tx(g,r.j+' '+fmt(r.kg)+' kg',24,y,26,'#eef5f0','left','700');tx(g,rp(r.kg*J[r.j].h),W-24,y,26,'#eef5f0','right','700')});
 if(!S.cart.length){tx(g,'Keranjang masih kosong',24,226,28,'#eef5f0','left','700');tx(g,'Isi dulu di Daftar Harga',24,262,24,'#9fd9b8','left','700')}
 if(S.cart.length>2)tx(g,'+'+(S.cart.length-2)+' baris lagi',24,284,22,'#9fd9b8','left','700');
 tx(g,'Total '+rp(tot),24,H-96,38,'#f3c64f');
 if(S.cart.length){B(24,H-76,290,62,'Timbang sekarang',S.busy?null:()=>doSetor(),'g');B(330,H-76,158,62,'Hapus',S.busy?null:()=>{S.cart=[];S.m.tm='';redraw()},'n')}else B(24,H-76,464,62,'Pilih sampah dulu →',()=>goSec('rak'),'y')});
handOn(tmS,169,373,'timbang',1);lab(tm,0,3.5,0,'3','Timbang',2.4,'timbang');ring(tm,2,'timbang');

// 3 PRINTER STRUK
const pr=new THREE.Group();pr.position.set(-.8,0,1.8);sc.add(pr);
box(1.7,.9,1.3,cream,0,.45,0,pr);box(1.7,.5,.9,cream,0,1.12,-.2,pr);box(1.3,.08,.1,M(0x26332d),0,1.0,.62,pr);
const prS=screen(1.5,.55,0,.42,.66,pr);
const paper=screen(1.3,1.5,0,1.06,.4,pr);paper.scale.y=.01;paper.rotation.x=-.12;
prS.paint((g,W,H,B)=>{bg(g,W,H);
 if(lastR){tx(g,lastR.k,20,44,34,'#fff');tx(g,rp(lastR.v),W-20,44,34,'#8ef0be','right');B(20,H-80,230,62,'Cetak lagi',S.busy?null:()=>printRcpt(lastR),'y');tx(g,'Saldo '+rp(lastR.s),W-20,H-38,22,'#9fd9b8','right','700')}
 else{tx(g,'Belum ada struk',20,50,34,'#fff');B(20,H-80,330,62,'Setor dulu →',()=>goSec('timbang'),'y')}});
function rcptDraw(r){paper.paint((g,W,H)=>{g.fillStyle='#fff';g.fillRect(0,0,W,H);
 const L=r?[['BANK SAMPAH','c',40],['SAHABAT IBU','c',34],[r.k+' · '+r.t,'c',30],['-'],...r.rows.slice(0,3),['-'],['TOTAL',rp(r.v),40],['Saldo '+rp(r.s),'c',32],['Terima kasih, Ani ','c',30]]:[['Belum ada struk','c',34]];
 let y=62;L.forEach(l=>{if(l[0]=='-'){g.fillStyle='#999';g.fillRect(24,y-18,W-48,3);y+=18;return}
  if(l[1]=='c')tx(g,l[0],W/2,y,l[2],'#222','center');else{tx(g,l[0],28,y,l[2],'#222','left','700');tx(g,l[1],W-28,y,l[2],'#222','right','700')}y+=48})})}
handOn(prS,135,145,'struk',1,()=>!!lastR);lab(pr,0,2.75,0,'4','Struk',2.2,'struk');ring(pr,1.3,'struk');

// 4 BUKU TABUNGAN
const bk=new THREE.Group();bk.position.set(2.8,0,-.4);sc.add(bk);
box(4.4,.1,2.4,M(0xa9774a,.7),0,1.0,0,bk);[[-2,-1],[2,-1],[-2,1],[2,1]].forEach(p=>box(.12,1,.12,M(0x8a5f38),p[0],.5,p[1],bk));
box(3.9,.8,.5,M(0x5a3d28),0,1.5,-.6,bk);
const bb=new THREE.Group();bb.position.set(0,1.82,.05);bb.rotation.x=-.8;bk.add(bb);
box(4.1,2.25,.1,M(0x2f5d46,.8),0,0,-.09,bb);
function pg(x,ry){const g=new THREE.Group();g.position.set(x,0,0);g.rotation.y=ry;bb.add(g);box(1.95,2.1,.06,M(0xf6f0df,.9),0,0,-.03,g);return screen(1.9,2.0,0,0,.01,g)}
const bL=pg(-1.0,.04),bR=pg(1.0,-.04);box(.05,.1,1.8,M(0xc63a2d),0,-.02,.05,bb).rotation.x=Math.PI/2;
const flt=()=>S.riw.filter(r=>S.f=='all'||(S.f=='set'&&r.v>0)||(S.f=='tar'&&r.v<0));
bL.paint((g,W,H,B)=>{g.fillStyle='#f6f0df';g.fillRect(0,0,W,H);tx(g,'Buku Tabungan',W/2,62,40,'#3a2f1e','center');tx(g,'Ani · NSB-0842',W/2,102,28,'#6b5a3d','center','700');
 tx(g,'Uang bisa diambil',40,166,28,'#6b5a3d','left','700');tx(g,rp(avail()),40,228,62,'#1f6b46');tx(g,'Sedang diajukan '+rp(S.tahan),40,268,25,'#a0701a','left','700');
 [['all','Semua'],['set','Setoran'],['tar','Tarikan']].forEach((f,i)=>B(40,292+i*76,432,66,f[1],()=>{S.f=f[0];redraw()},S.f==f[0]?'pon':'p'))});
bR.paint((g,W,H)=>{g.fillStyle='#f6f0df';g.fillRect(0,0,W,H);tx(g,'Catatan',40,62,40,'#3a2f1e');tx(g,flt().length+' catatan',W-40,62,26,'#6b5a3d','right','700');const L=flt().slice(0,4);if(!L.length)tx(g,'Belum ada catatan.',40,150,28,'#6b5a3d','left','700');
 L.forEach((r,i)=>{const y=126+i*98;g.fillStyle='#d9cfb5';g.fillRect(40,y-26,W-80,3);tx(g,r.k+' · '+r.t,40,y+10,28,'#3a2f1e');tx(g,(r.v<0?'−':'+')+Math.abs(Math.round(r.v)).toLocaleString('id-ID'),W-40,y+10,34,r.v<0?'#b03a2d':'#1f6b46','right');tx(g,r.d,40,y+46,25,'#6b5a3d','left','700')});tx(g,'Halaman 1',W/2,H-18,22,'#9a8a68','center','700')});
handOn(bL,420,397,'riwayat',1.15);lab(bk,0,3.0,0,'5','Buku Tabungan',3.0,'riwayat');ring(bk,2.6,'riwayat');

// 5 RAK + KIOS HARGA
const rk=new THREE.Group();rk.position.set(-6.2,0,7.8);sc.add(rk);
const wood=M(0xc79a63,.7);[0,1.8,3.6].forEach(y=>box(4.2,.18,1.2,wood,0,y+.3,0,rk));[-2,2].forEach(x=>box(.2,4.0,1.2,wood,x,2.2,0,rk));
function mk(j,x,y){const g=new THREE.Group();g.position.set(x,y,0);rk.add(g);
 if(j=='Plastik')for(let i=0;i<3;i++){const b=cyl(.22,.9,M(0x8fc6ec,.2),i*.5-.5,.5,0,g);b.material.transparent=true;b.material.opacity=.85;cyl(.09,.2,M(0x3f7fb0),i*.5-.5,1.05,0,g)}
 if(j=='Kertas')for(let i=0;i<4;i++)box(1.5,.22,.9,M(i%2?0xd9b27a:0xc79b60),0,.15+i*.23,0,g).rotation.y=i*.05;
 if(j=='Logam')for(let i=0;i<3;i++)cyl(.26,.7,M(0xcdd3d8,.25,.9),i*.6-.6,.4,0,g);
 screen(1.5,.4,0,-.05,.65,g).paint((gg,W,H)=>{bg(gg,W,H,'#fff');tx(gg,j+' '+Math.round(J[j].h/1000)+'rb/kg',W/2,H*.68,44,'#222','center')});
 g.traverse(o=>{o.userData.j=j});g.scale.setScalar(1.25);PILES[j]=g;g.by=y}
mk('Plastik',-1.1,.4);mk('Kertas',1.1,.4);mk('Logam',0,2.2);
screen(3.4,.6,0,4.5,.62,rk).paint((g,W,H)=>{bg(g,W,H,'#2f7d5b');tx(g,'DAFTAR HARGA SAMPAH',W/2,H*.65,46,'#fff','center')});
const kg=new THREE.Group();kg.position.set(3.7,0,.9);rk.add(kg);box(.14,1.2,.14,M(0x26332d),0,.6,-.1,kg);box(2.3,1.95,.12,M(0x26332d),0,1.95,-.08,kg);
const kS=screen(2.2,1.85,0,1.95,0,kg);
kS.paint((g,W,H,B)=>{bg(g,W,H);if(S.m.rak)tx(g,S.m.rak,20,34,22,'#f3c64f','left','700');else tx(g,'Harga hari ini · 22 Nov 2026',20,34,22,'#9fd9b8','left','700');
 Object.keys(J).forEach((k,i)=>B(20+i*162,48,150,64,k,()=>{S.sel=k;redraw()},k==S.sel?'on':'n'));
 tx(g,rp(J[S.sel].h)+'/kg',20,172,58);tx(g,J[S.sel].d,20,204,20,'#cfe9da','left','700');
 B(20,216,92,66,'−',()=>{S.kg=Math.max(.25,S.kg-.25);redraw()});tx(g,fmt(S.kg)+' kg',W/2,266,46,'#fff','center');B(400,216,92,66,'+',()=>{S.kg=Math.min(20,S.kg+.25);redraw()});
 tx(g,'Nilai '+rp(S.kg*J[S.sel].h),20,326,34,'#f3c64f');B(20,346,472,70,'Masukkan keranjang',()=>{S.cart.push({j:S.sel,kg:S.kg});S.m.rak=''+S.sel+' '+fmt(S.kg)+' kg masuk keranjang';S.m.tm='';toast(''+S.sel+' '+fmt(S.kg)+' kg masuk keranjang. Lanjut ke Timbangan.');redraw()},'g')});
handOn(kS,256,381,'rak',1.05);lab(rk,1.4,5.5,0,'2','Daftar Harga',3.0,'rak');ring(rk,3.8,'rak',.6);

// 6 MESIN AMBIL UANG (capit)
const cp=new THREE.Group();cp.position.set(-4.2,0,-2.2);sc.add(cp);
box(3.6,2.2,2.4,cream,0,1.1,0,cp);
const gl=new THREE.Mesh(new THREE.BoxGeometry(3.4,1.7,2.2),new THREE.MeshPhysicalMaterial({color:0xcfeee0,transparent:true,opacity:.18,roughness:.05}));gl.position.y=3.05;cp.add(gl);
[[-1.7,-1.1],[1.7,-1.1],[-1.7,1.1],[1.7,1.1]].forEach(p=>box(.1,1.7,.1,M(0x26332d),p[0],3.05,p[1],cp));box(3.6,.12,2.4,cream,0,3.96,0,cp);
box(3.2,.5,.1,M(0xe5594b),0,4.3,1.03,cp);
const cpL=screen(3.1,.46,0,4.3,1.09,cp);cpL.paint((g,W,H)=>{bg(g,W,H,'#e5594b');tx(g,'AMBIL UANG',W/2,H*.7,56,'#fff','center')});
const CY=3.7,DY=.5;
const envs=[];for(let i=0;i<5;i++){const e=box(.7,.12,.45,M([0xffffff,0xe4f5ea,0xfff6d8,0xffffff,0xe4f5ea][i]),-1.3+i*.4,2.32+(i%2)*.12,-.4+(i%3)*.4,cp);e.rotation.y=i*.7;envs.push(e)}
box(.9,.1,.9,M(0x111),1.2,2.25,.2,cp);
const claw=new THREE.Group();claw.position.set(0,CY,0);cp.add(claw);box(.3,.14,.3,M(0x26332d),0,0,0,claw);cyl(.04,.65,M(0x26332d),0,-.33,0,claw);
const fg=[];for(let i=0;i<3;i++)fg.push(box(.06,.45,.06,M(0x555,.3,.8),Math.cos(i*2.09)*.22,-.75,Math.sin(i*2.09)*.22,claw));
const cpS=screen(3.3,2.0,0,1.1,1.21,cp);
cpS.paint((g,W,H,B)=>{bg(g,W,H);B(12,10,244,52,'Ambil uang',()=>{S.tab='ambil';redraw()},S.tab=='ambil'?'on':'n');B(260,10,240,52,'Status ('+S.tarik.length+')',()=>{S.tab='status';redraw()},S.tab=='status'?'on':'n');
 if(S.tab=='ambil'){tx(g,'Tersedia '+rp(avail()),W/2,102,28,'#f3c64f','center','700');
  B(12,116,92,76,'−',()=>{S.nom=Math.max(10000,S.nom-10000);redraw()});tx(g,rp(S.nom),W/2,170,54,'#fff','center');B(408,116,92,76,'+',()=>{S.nom=Math.min(Math.floor(avail()/10000)*10000,S.nom+10000);redraw()});
  B(12,204,488,70,'Ambil uang',S.busy?null:()=>doCapit(),'r');tx(g,S.m.cp||'Uang disiapkan, ambil tunai di pengurus.',W/2,298,21,'#9fd9b8','center','700')}
 else{if(!S.tarik.length){tx(g,'Belum ada pengajuan.',W/2,130,30,'#9fd9b8','center','700');tx(g,'Tekan tab “Ambil uang” dulu',W/2,172,24,'#9fd9b8','center','700')}
  S.tarik.slice(0,2).forEach((t,i)=>{const y=70+i*116;g.fillStyle='#ffffff10';rr(g,10,y,492,108,16);g.fill();tx(g,t.id,26,y+34,28,'#fff');tx(g,rp(t.v),W-26,y+34,28,'#8ef0be','right');tx(g,t.st=='Dicairkan'?'Dicairkan':''+t.st,26,y+64,22,t.st=='Dicairkan'?'#6fe0a2':'#f3c64f','left','700');
   if(t.st=='Menunggu'){B(26,y+54,110,46,'Batal',()=>aksi(t,'batal'),'r');B(148,y+54,340,46,'Pengurus: setujui',()=>aksi(t,'next'),'y')}
   if(t.st=='Disetujui')B(190,y+54,298,46,'Pengurus: beri tunai',()=>aksi(t,'next'),'g')})}});
handOn(cpS,440,196,'capit',1.2,()=>S.tab=='ambil');lab(cp,0,4.85,0,'6','Ambil Uang',3.2,'capit');ring(cp,2.7,'capit');