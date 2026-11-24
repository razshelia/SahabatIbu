// Ruangan 3D di halaman biasa.
// - Tamu (login/daftar): adegan sama, tanpa interaksi (seperti sebelumnya).
// - Sudah login (window.GARASI dari layouts/app.blade.php): alat 3D menampilkan data nyata
//   sesuai peran, dan alat bisa diketuk untuk membuka halaman yang sesuai.
const G=window.GARASI||null,RM=matchMedia('(prefers-reduced-motion:reduce)').matches,ROOT=document.documentElement;
const VW={home:{p:[0,3.4,13.2],t:[0,2,-1]},timbang:{p:[8.5,3,10],t:[4.2,1.6,4.8]},struk:{p:[3,3,8],t:[-1.2,1.4,1.8]},capit:{p:[-.8,3.4,6.5],t:[-4.2,2.2,-2.2]},rak:{p:[-.5,3.6,15],t:[-6.2,2.2,7.8]},luar:{p:[0,5,32],t:[0,3,14.3],pintu:1},scan:{p:[12,3.4,16],t:[7.2,1.8,9.6]},riwayat:{p:[.4,3.4,7],t:[4.6,1.6,-.4]}};
HD.forEach(x=>x.visible=false);LB.forEach(x=>x.sp.visible=false);RG.forEach(x=>x.m.visible=false);
const T0=performance.now();

if(!G){
 const K=VW[document.body.dataset.view]||VW.luar;sw.visible=K===VW.luar;
 const size=()=>{R.setSize(innerWidth,innerHeight);cam.aspect=innerWidth/innerHeight;cam.updateProjectionMatrix()};
 addEventListener('resize',size);size();rcptDraw(null);redraw();
 (function loop(){requestAnimationFrame(loop);const t=(performance.now()-T0)/1e3,o=RM||!K.pintu?1:sm(cl(t/2.4));
  dr.scale.y=K.pintu?1-.92*o:.08;dl.intensity=K.pintu?o*1.3:1.3;
  const w=RM?0:1;cam.position.set(K.p[0]+Math.sin(t/3)*.35*w,K.p[1]+Math.cos(t/4)*.12*w,K.p[2]);cam.lookAt(K.t[0],K.t[1],K.t[2]);R.render(sc,cam)})();
}else{
 sw.visible=false;dr.scale.y=.08;dl.intensity=1.3;
 const ADM=G.role==='admin',N=G.nasabah||{},A=G.admin||{},H=G.harga||[],L=G.label||{};
 const ST={menunggu:['Menunggu','#f3c64f'],disetujui:['Disetujui','#f3c64f'],dicairkan:['Dicairkan','#6fe0a2'],ditolak:['Ditolak','#ff8a7a'],dibatalkan:['Dibatalkan','#ff8a7a']};
 const st=s=>ST[s]||[s?s[0].toUpperCase()+s.slice(1):'-','#9fd9b8'];
 const tf=(g,s,x,y,sz,col,a,wt,mw)=>{s=String(s==null?'':s);wt=wt||'800';while(sz>14){g.font=wt+' '+sz+'px Trebuchet MS';if(g.measureText(s).width<=mw)break;sz-=2}tx(g,s,x,y,sz,col,a,wt)};
 const foot=(g,W,H,s,c='#9fd9b8')=>tx(g,s,W/2,H-14,20,c,'center','700');
 const kg=v=>fmt(v)+' kg';
 const DR={};// draf dari isian form di halaman (lihat sinkron.js)
 const arrow=(g,x,y,up)=>{g.fillStyle=up?'#6fe0a2':'#ff8a7a';g.beginPath();up?(g.moveTo(x,y-14),g.lineTo(x+13,y+6),g.lineTo(x-13,y+6)):(g.moveTo(x,y+6),g.lineTo(x+13,y-14),g.lineTo(x-13,y-14));g.closePath();g.fill()};

 // ---------- isi layar tiap alat ----------
 const harga=(g,W,Hh)=>{bg(g,W,Hh);tx(g,'Harga hari ini',20,36,26,'#9fd9b8','left','700');tx(g,'per kg',W-20,36,22,'#9fd9b8','right','700');
  if(!H.length)tx(g,'Belum ada harga.',20,120,30,'#eef5f0','left','700');
  H.slice(0,6).forEach((j,i)=>{const y=82+i*56;g.fillStyle='#ffffff12';rr(g,12,y-34,W-24,48,14);g.fill();tf(g,j.n,24,y,30,'#fff','left','800',250);
   const dj=DR.rak&&DR.rak.n===j.n&&DR.rak.h>0?DR.rak:null;tx(g,rp(dj?dj.h:j.h),W-52,y,30,dj?'#f3c64f':'#8ef0be','right');
   if(dj){if(dj.h!==j.h)arrow(g,W-30,y-8,dj.h>j.h)}else if(j.p!=null&&j.p!==j.h)arrow(g,W-30,y-8,j.h>j.p)});
  foot(g,W,Hh,DR.rak&&DR.rak.h>0?'Harga baru, belum disimpan':'Pilih untuk membuka '+L.rak)};
 kS.paint(harga);

 if(!ADM){
  const last=(N.setoran||[])[0],tr=N.tarik||[];
  pmS.paint((g,W,Hh)=>{bg(g,W,Hh);tx(g,'Saldo tersedia',24,46,28,'#9fd9b8','left','700');tf(g,rp(N.tersedia||0),24,122,66,'#8ef0be','left','800',W-48);
   tx(g,N.tahan>0?'Saldo ditahan '+rp(N.tahan):'Saldo total '+rp(N.saldo||0),24,164,26,'#f3c64f','left','700');
   tf(g,(N.nomor||'')+' · '+G.nama,24,Hh-52,24,'#fff','left','800',W-48);foot(g,W,Hh,'Pilih untuk melihat '+L.scan)});
  tmS.paint((g,W,Hh)=>{bg(g,W,Hh);tx(g,'Setoran saya',24,46,30,'#9fd9b8','left','700');tf(g,kg(N.berat||0),24,136,78,'#fff','left','800',W-48);
   tx(g,(N.jumlah||0)+' setoran berhasil',24,176,24,'#f3c64f','left','700');
   if(last){tx(g,last.k+' · '+last.t,24,226,24,'#9fd9b8','left','700');last.rows.slice(0,2).forEach((r,i)=>{tf(g,r.j+' '+kg(r.kg),24,262+i*34,26,'#eef5f0','left','700',300);tx(g,rp(r.v),W-24,262+i*34,26,'#eef5f0','right','700')});tx(g,'Total '+rp(last.v),24,Hh-78,36,'#f3c64f')}
   else tx(g,'Belum ada setoran',24,240,30,'#eef5f0','left','700');foot(g,W,Hh,'Pilih untuk melihat '+L.timbang)});
  prS.paint((g,W,Hh)=>{bg(g,W,Hh);if(last){tx(g,last.k,20,52,36,'#fff');tx(g,rp(last.v),W-20,52,36,'#8ef0be','right');tx(g,'Setoran terakhir · '+last.t,20,100,24,'#9fd9b8','left','700')}else tx(g,'Belum ada struk',20,56,34,'#fff');foot(g,W,Hh,'Pilih untuk melihat '+L.struk)});
  paper.paint((g,W,Hh)=>{g.fillStyle='#fff';g.fillRect(0,0,W,Hh);let y=62;const c=(s,sz)=>{tx(g,s,W/2,y,sz,'#222','center');y+=48};
   if(!last){c('Belum ada struk',34);return}c('BANK SAMPAH',40);c('SAHABAT IBU',34);c(last.k+' · '+last.t,30);g.fillStyle='#999';g.fillRect(24,y-18,W-48,3);y+=18;
   last.rows.slice(0,3).forEach(r=>{tf(g,r.j+' '+kg(r.kg),28,y,28,'#222','left','700',300);tx(g,rp(r.v),W-28,y,28,'#222','right','700');y+=48});
   g.fillStyle='#999';g.fillRect(24,y-18,W-48,3);y+=18;tx(g,'TOTAL',28,y,40,'#222','left','700');tx(g,rp(last.v),W-28,y,40,'#222','right','700');y+=56;c('Terima kasih, '+G.nama.split(' ')[0],30)});
  bL.paint((g,W,Hh)=>{g.fillStyle='#f6f0df';g.fillRect(0,0,W,Hh);tx(g,'Buku Tabungan',W/2,62,40,'#3a2f1e','center');tf(g,G.nama,W/2,102,28,'#6b5a3d','center','700',W-60);tx(g,N.nomor||'',W/2,136,24,'#6b5a3d','center','700');
   tx(g,'Saldo tersedia',40,200,28,'#6b5a3d','left','700');tf(g,rp(N.tersedia||0),40,266,60,'#1f6b46','left','800',W-80);
   tx(g,'Saldo ditahan '+rp(N.tahan||0),40,310,26,'#a0701a','left','700');tx(g,'Saldo total '+rp(N.saldo||0),40,350,26,'#6b5a3d','left','700');foot(g,W,Hh,'Pilih untuk melihat '+L.riwayat,'#9a8a68')});
  bR.paint((g,W,Hh)=>{g.fillStyle='#f6f0df';g.fillRect(0,0,W,Hh);tx(g,'Catatan',40,62,40,'#3a2f1e');const l=N.setoran||[];tx(g,l.length+' terbaru',W-40,62,26,'#6b5a3d','right','700');
   if(!l.length)tx(g,'Belum ada catatan.',40,150,28,'#6b5a3d','left','700');
   l.slice(0,4).forEach((r,i)=>{const y=126+i*98;g.fillStyle='#d9cfb5';g.fillRect(40,y-26,W-80,3);tx(g,r.k+' · '+r.t,40,y+10,28,'#3a2f1e');tx(g,'+'+Math.round(r.v).toLocaleString('id-ID'),W-40,y+10,34,'#1f6b46','right');
    tf(g,r.rows.map(x=>x.j+' '+kg(x.kg)).join(', '),40,y+46,24,'#6b5a3d','left','700',W-80)});tx(g,'Halaman 1',W/2,Hh-18,22,'#9a8a68','center','700')});
  cpS.paint((g,W,Hh)=>{bg(g,W,Hh);tf(g,'Saldo tersedia',24,48,26,'#9fd9b8','left','700',230);tf(g,rp(N.tersedia||0),24,122,60,'#8ef0be','left','800',230);
   tf(g,DR.capit>0?'Akan diajukan '+rp(DR.capit):(N.tahan>0?'Ditahan '+rp(N.tahan):'Tidak ada yang ditahan'),24,164,24,DR.capit>0?'#ffffff':'#f3c64f','left','700',230);
   tx(g,'Pengajuan terakhir',268,48,24,'#9fd9b8','left','700');if(!tr.length)tx(g,'Belum ada pengajuan',268,100,24,'#eef5f0','left','700');
   tr.slice(0,3).forEach((t,i)=>{const y=96+i*62,s=st(t.st);g.fillStyle='#ffffff10';rr(g,262,y-30,W-274,54,14);g.fill();tf(g,rp(t.v),274,y,28,'#fff','left','800',130);tx(g,s[0],W-26,y,24,s[1],'right','700')});
   foot(g,W,Hh,'Pilih untuk melihat '+L.capit)});
 }else{
  const dpl=A.terbaru,ln=A.log||[],gr=A.grafik||[],aq=A.antrian||[];
  pmS.paint((g,W,Hh)=>{bg(g,W,Hh);tx(g,'Pendaftar menunggu',24,46,28,'#9fd9b8','left','700');tx(g,(A.pendaftar||0)+' orang',24,122,66,A.pendaftar>0?'#f3c64f':'#8ef0be');
   tx(g,'Aktif '+(A.aktif||0)+' · Nonaktif '+(A.nonaktif||0),24,168,26,'#eef5f0','left','700');foot(g,W,Hh,'Pilih untuk melihat '+L.scan)});
  tmS.paint((g,W,Hh)=>{if(DR.timbang){bg(g,W,Hh);tx(g,'Sedang ditimbang',24,46,30,'#9fd9b8','left','700');tf(g,kg(DR.timbang.kg),24,142,78,'#fff','left','800',W-48);
   tf(g,DR.timbang.nama||'Pilih nasabah dulu',24,190,26,'#f3c64f','left','700',W-48);tf(g,rp(DR.timbang.v),24,Hh-78,44,'#8ef0be','left','800',W-48);foot(g,W,Hh,'Belum disimpan');return}
   bg(g,W,Hh);tx(g,'Setoran hari ini',24,46,30,'#9fd9b8','left','700');tx(g,(A.hari_n||0)+' transaksi',24,132,66);
   tx(g,kg(A.hari_kg||0)+' · '+rp(A.hari_rp||0),24,178,26,'#f3c64f','left','700');tx(g,'Bulan ini',24,238,26,'#9fd9b8','left','700');
   tx(g,(A.bulan_n||0)+' transaksi · '+kg(A.bulan_kg||0),24,276,26,'#eef5f0','left','700');tf(g,rp(A.bulan_rp||0),24,Hh-78,40,'#f3c64f','left','800',W-48);foot(g,W,Hh,'Pilih untuk melihat '+L.timbang)});
  prS.paint((g,W,Hh)=>{bg(g,W,Hh);tf(g,G.nama,20,52,34,'#fff','left','800',W-40);tx(g,'Pengurus · '+ln.length+' aktivitas terbaru',20,100,24,'#9fd9b8','left','700');foot(g,W,Hh,'Pilih untuk melihat '+L.struk)});
  paper.paint((g,W,Hh)=>{g.fillStyle='#fff';g.fillRect(0,0,W,Hh);tx(g,'RIWAYAT AKTIVITAS',W/2,56,38,'#222','center');g.fillStyle='#999';g.fillRect(24,70,W-48,3);
   if(!ln.length)tx(g,'Belum ada aktivitas',W/2,130,28,'#222','center','700');
   ln.slice(0,7).forEach((l,i)=>{const y=112+i*66;tx(g,l.w||'',28,y,22,'#777','left','700');tf(g,l.a||'',28,y+30,28,'#222','left','800',W-56)})});
  bL.paint((g,W,Hh)=>{g.fillStyle='#f6f0df';g.fillRect(0,0,W,Hh);tx(g,'Laporan',W/2,62,40,'#3a2f1e','center');tx(g,'Bulan ini',W/2,102,28,'#6b5a3d','center','700');
   tf(g,rp(A.bulan_rp||0),40,190,60,'#1f6b46','left','800',W-80);tx(g,(A.bulan_n||0)+' transaksi',40,250,28,'#3a2f1e','left','700');tx(g,kg(A.bulan_kg||0)+' sampah',40,296,28,'#3a2f1e','left','700');
   tx(g,'Hari ini '+rp(A.hari_rp||0),40,350,26,'#a0701a','left','700');foot(g,W,Hh,'Pilih untuk melihat '+L.riwayat,'#9a8a68')});
  bR.paint((g,W,Hh)=>{g.fillStyle='#f6f0df';g.fillRect(0,0,W,Hh);tx(g,'6 bulan terakhir',W/2,62,34,'#3a2f1e','center');const mx=Math.max(1,...gr.map(x=>x.v)),bw=(W-80)/Math.max(1,gr.length);
   gr.forEach((x,i)=>{const h=Math.max(6,(x.v/mx)*300),bx=40+i*bw+8;g.fillStyle=i===gr.length-1?'#1f6b46':'#9cc4ac';rr(g,bx,Hh-90-h,bw-16,h,10);g.fill();tx(g,x.b,bx+(bw-16)/2,Hh-56,24,'#3a2f1e','center','700')});
   tx(g,'Rp '+Math.round(mx/1000).toLocaleString('id-ID')+' rb tertinggi',W/2,Hh-18,22,'#9a8a68','center','700')});
  cpS.paint((g,W,Hh)=>{bg(g,W,Hh);tf(g,'Pengajuan penarikan',24,48,26,'#9fd9b8','left','700',230);tf(g,(A.tarik_menunggu||0)+' menunggu',24,122,50,A.tarik_menunggu>0?'#f3c64f':'#8ef0be','left','800',230);
   tf(g,(A.tarik_siap||0)+' disetujui',24,164,24,'#eef5f0','left','700',230);tx(g,'Pengajuan',268,48,24,'#9fd9b8','left','700');if(!aq.length)tx(g,'Tidak ada pengajuan',268,100,24,'#eef5f0','left','700');
   aq.slice(0,3).forEach((t,i)=>{const y=96+i*62,s=st(t.st);g.fillStyle='#ffffff10';rr(g,262,y-30,W-274,54,14);g.fill();tf(g,t.who||t.k,274,y,24,'#fff','left','800',110);tx(g,rp(t.v),W-20,y,24,s[1],'right','700')});
   foot(g,W,Hh,'Pilih untuk melihat '+L.capit)});
 }

 // ---------- papan nama di atas alat (bisa diketuk) ----------
 const TOOL={scan:pm,timbang:tm,struk:pr,riwayat:bk,rak:rk,capit:cp};
 const LP={scan:[0,2.95,0,2.5],timbang:[0,3.5,0,2.4],struk:[0,2.75,0,2.3],riwayat:[0,3.0,0,3.0],rak:[1.4,5.5,0,3.0],capit:[0,4.85,0,3.2]};
 const badge={scan:ADM?A.pendaftar:0,capit:ADM?A.tarik_menunggu:0};
 const mkLab=(k,fresh)=>{const c=document.createElement('canvas');c.width=640;c.height=160;const g=c.getContext('2d'),b=badge[k]||0;
  g.fillStyle=k===G.fokus?'#1d6a46':'#2f7d5b';rr(g,6,6,628,148,74);g.fill();g.lineWidth=k===G.fokus?9:6;g.strokeStyle=k===G.fokus?'#f3c64f':'#ffffff';g.stroke();
  g.fillStyle='#f3c64f';g.beginPath();g.arc(80,80,52,0,7);g.fill();
  if(b>0)tx(g,b>99?'99+':String(b),80,102,b>9?54:66,'#2b1f00','center');
  else{g.strokeStyle='#2b1f00';g.lineWidth=12;g.lineCap='round';g.lineJoin='round';g.beginPath();g.moveTo(68,56);g.lineTo(94,80);g.lineTo(68,104);g.stroke()}
  tf(g,L[k]||k,372,100,56,'#fff','center','800',430);
  if(fresh){g.fillStyle='#ff8a7a';g.beginPath();g.arc(612,34,22,0,7);g.fill();tx(g,'!',612,46,34,'#2b1f00','center')}
  const sp=new THREE.Sprite(new THREE.SpriteMaterial({map:new THREE.CanvasTexture(c),transparent:true,depthTest:false}));const p=LP[k],P=TOOL[k];
  sp.scale.set(p[3],p[3]/4,1);sp.position.set(p[0],p[1],p[2]);sp.renderOrder=8;sp.userData.k=k;P.add(sp);return sp};

 // ---------- reaksi: data berubah sejak kunjungan terakhir ----------
 const KEY='garasi_sig_'+G.role+'_'+G.uid,pulse={};let prev=null;
 try{prev=JSON.parse(localStorage.getItem(KEY)||'null');localStorage.setItem(KEY,JSON.stringify(G.sig))}catch(e){}
 const fresh={};
 window.ALAT={set(k,v){DR[k]=v;const m={capit:cpS,timbang:tmS,rak:kS}[k];m&&m.render()},pulse(k){if(TOOL[k])pulse[k]=performance.now()}};
 if(prev&&G.sig){const d=(a,b)=>a!==b;
  if(d(prev.setoran,G.sig.setoran)){['timbang','struk','riwayat'].forEach(k=>fresh[k]=1)}
  if(d(prev.tarik,G.sig.tarik))fresh.capit=1;
  if(d(prev.harga,G.sig.harga))fresh.rak=1}
 const LS={};Object.keys(TOOL).forEach(k=>LS[k]=mkLab(k,fresh[k]));
 Object.keys(fresh).forEach(k=>pulse[k]=performance.now()+700);

 // ---------- ring sorotan alat halaman ini ----------
 RG.forEach(r=>{if(r.k===G.fokus){r.m.visible=true;r.m.material.opacity=.4}});

 // ---------- bingkai: 3D mengisi area bebas (kolom kanan / jendela atas) ----------
 let reg={x:0,y:0,w:innerWidth,h:innerHeight},pull=1;
 const cur={p:[0,0,0],t:[0,0,0],init:false};
 function layout(){const W=innerWidth,Hh=innerHeight,full=ROOT.classList.contains('lihat');let col=0,win=0;
  if(!full){if(W>=900)col=Math.round(Math.min(W*.38,520));else win=Math.round(Math.min(Hh*.4,340))}
  ROOT.style.setProperty('--col',col+'px');ROOT.style.setProperty('--win',win+'px');
  reg=full?{x:0,y:0,w:W,h:Hh}:col?{x:W-col-14,y:78,w:col,h:Hh-78-14}:{x:0,y:76,w:W,h:win};
  R.setSize(W,Hh);cam.aspect=W/Hh;
  const ra=Math.max(W/reg.w,Hh/reg.h);pull=full||G.fokus==='home'?1:cl(Math.pow(ra,.72),1,2.8);cam.fov=full?42:G.fokus==='home'?cl(42+(Math.pow(ra,.7)-1)*30,42,80):42;
  const dx=reg.x+reg.w/2-W/2,dy=reg.y+reg.h/2-Hh/2;
  dx||dy?cam.setViewOffset(W,Hh,-dx,-dy,W,Hh):cam.clearViewOffset();cam.updateProjectionMatrix()}
 ROOT.classList.add('g3d');addEventListener('resize',layout);layout();redraw();

 // ---------- ketuk alat -> pindah halaman ----------
 const ray=new THREE.Raycaster(),mv=new THREE.Vector2();
 function pick(e){const r=R.domElement.getBoundingClientRect();mv.set((e.clientX-r.left)/r.width*2-1,-((e.clientY-r.top)/r.height)*2+1);ray.setFromCamera(mv,cam);
  const h=ray.intersectObjects(hit,true);for(const x of h){let o=x.object;while(o){if(o.userData&&o.userData.k&&TOOL[o.userData.k])return o.userData.k;o=o.parent}}return null}
 let d0=null;const cv=R.domElement;
 // ---------- geser latar: seret untuk memutar sudut pandang, klik dua kali / tombol untuk memusatkan ----------
 const ORB={y:0,p:0,ty:0,tp:0},YMAX=.9,PMIN=-.18,PMAX=.4,pusat=$('pusat');let drag=null;
 const orbSet=(y,p)=>{ORB.ty=cl(y,-YMAX,YMAX);ORB.tp=cl(p,PMIN,PMAX);pusat&&pusat.classList.toggle('on',Math.abs(ORB.ty)>.03||Math.abs(ORB.tp)>.03)};
 pusat&&pusat.addEventListener('click',()=>orbSet(0,0));
 cv.addEventListener('dblclick',()=>orbSet(0,0));
 cv.addEventListener('pointerdown',e=>{d0={x:e.clientX,y:e.clientY,t:performance.now()};drag={x:e.clientX,y:e.clientY,by:ORB.ty,bp:ORB.tp,on:false,id:e.pointerId}});
 cv.addEventListener('pointermove',e=>{if(!drag||e.pointerId!==drag.id)return;const dx=e.clientX-drag.x,dy=e.clientY-drag.y;
  if(!drag.on){if(Math.hypot(dx,dy)<8)return;drag.on=true;cv.classList.add('drag');try{cv.setPointerCapture(e.pointerId)}catch(_){}}
  orbSet(drag.by-dx*.006,drag.bp+dy*.004)});
 const endDrag=()=>{if(drag&&drag.on)cv.classList.remove('drag');drag=null};
 cv.addEventListener('pointerup',endDrag);cv.addEventListener('pointercancel',endDrag);
 cv.addEventListener('pointerup',e=>{if(!d0)return;const m=Math.hypot(e.clientX-d0.x,e.clientY-d0.y),dt=performance.now()-d0.t;d0=null;if(m>10||dt>700)return;
  const k=pick(e),u=k&&G.links[k];if(!u)return;pulse[k]=performance.now();if(u.split('#')[0]===location.href.split('#')[0])return;setTimeout(()=>{location.href=u},260)});
 let hv=0;cv.addEventListener('pointermove',e=>{if(e.pointerType!=='mouse'||hv)return;hv=1;requestAnimationFrame(()=>{hv=0;cv.style.cursor=pick(e)?'pointer':''})});

 // ---------- gambar ----------
 (function loop(){requestAnimationFrame(loop);const now=performance.now(),t=(now-T0)/1e3,full=ROOT.classList.contains('lihat'),K=full||!VW[G.fokus]?VW.home:VW[G.fokus];
  const tp=[0,1,2].map(i=>K.t[i]),pp=[0,1,2].map(i=>K.t[i]+(K.p[i]-K.t[i])*pull);
  if(!cur.init||RM){cur.p=pp;cur.t=tp;cur.init=true}else for(let i=0;i<3;i++){cur.p[i]=lerp(cur.p[i],pp[i],.08);cur.t[i]=lerp(cur.t[i],tp[i],.08)}
  ORB.y=lerp(ORB.y,ORB.ty,RM?1:.14);ORB.p=lerp(ORB.p,ORB.tp,RM?1:.14);
  const vx=cur.p[0]-cur.t[0],vz=cur.p[2]-cur.t[2],cy=Math.cos(ORB.y),sy=Math.sin(ORB.y),hd=Math.hypot(vx,vz);
  const w=RM?0:1;cam.position.set(cur.t[0]+vx*cy+vz*sy+Math.sin(t/3)*.3*w,cur.p[1]+hd*ORB.p+Math.cos(t/4)*.1*w,cur.t[2]-vx*sy+vz*cy);cam.lookAt(cur.t[0],cur.t[1],cur.t[2]);
  Object.keys(TOOL).forEach(k=>{const p=pulse[k];let s=1;if(p!=null){const q=(now-p)/1300;if(q>=1)delete pulse[k];else if(q>=0)s=1+.09*Math.sin(Math.PI*q)}TOOL[k].scale.setScalar(s)});
  RG.forEach(r=>{if(r.k===G.fokus)r.m.material.opacity=.3+.15*Math.sin(t*2.4)});
  R.render(sc,cam)})();
}