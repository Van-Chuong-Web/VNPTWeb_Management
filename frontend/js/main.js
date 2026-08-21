/**
 * VNPT — Main UI Helper Module
 * Xử lý hiệu ứng scroll, particles, mobile menu và nút cuộn lên đầu trang (Scroll to Top).
 */
(function () {
  'use strict';

  const escapeHtml = (str) => String(str || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');

  function initMain() {
    /* ---- Lucide icons ---- */
    if (window.lucide) lucide.createIcons();

    /* ---- Particles background ---- */
    const wrap = document.getElementById('particles-bg');
    if (wrap && wrap.children.length === 0) {
      const count = window.innerWidth < 700 ? 12 : 26;
      for (let i = 0; i < count; i++) {
        const dot = document.createElement('span');
        const size = 2 + Math.random() * 3;
        dot.style.width = size + 'px';
        dot.style.height = size + 'px';
        dot.style.left = Math.random() * 100 + 'vw';
        dot.style.top = Math.random() * 100 + 'vh';
        dot.style.animationDuration = (10 + Math.random() * 10) + 's';
        dot.style.animationDelay = (Math.random() * 6) + 's';
        wrap.appendChild(dot);
      }
    }

    /* ---- Navbar scroll shadow ---- */
    const navbar = document.getElementById('navbar');
    const onScroll = () => {
      if (!navbar) return;
      navbar.classList.toggle('scrolled', window.scrollY > 20);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* ---- Mobile hamburger menu ---- */
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.querySelector('.nav-links');
    if (hamburger && navLinks) {
      hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navLinks.classList.toggle('open');
      });
    }

    /* ---- Mobile dropdown toggles ---- */
    document.querySelectorAll('.nav-item.dropdown > .nav-link').forEach(link => {
      link.addEventListener('click', (e) => {
        if (window.innerWidth <= 960) {
          e.preventDefault();
          link.closest('.dropdown').classList.toggle('open');
        }
      });
    });

    /* ---- Close mobile menu after clicking a link ---- */
    document.querySelectorAll('.nav-links a:not(.nav-link)').forEach(a => {
      a.addEventListener('click', () => {
        hamburger?.classList.remove('active');
        navLinks?.classList.remove('open');
      });
    });

    /* ---- Scroll-reveal for counters & elements ---- */
    const counterEls = document.querySelectorAll('.stat-num, .hstat-num');
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseInt(el.getAttribute('data-target') || el.textContent.replace(/[^0-9]/g, ''), 10) || 0;
          if (target > 0) {
            let start = 0;
            const duration = 1200;
            const step = Math.max(1, Math.ceil(target / (duration / 16)));
            const timer = setInterval(() => {
              start += step;
              if (start >= target) {
                el.textContent = target;
                clearInterval(timer);
              } else {
                el.textContent = start;
              }
            }, 16);
          }
          counterObserver.unobserve(el);
        }
      });
    }, { threshold: 0.1 });
    counterEls.forEach(el => counterObserver.observe(el));



    /* ---- Category Filter Buttons ---- */
    const filterBtns = document.querySelectorAll('.filter-btn');
    const serviceCards = document.querySelectorAll('.service-card');

    if (filterBtns.length > 0) {
      filterBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
          e.preventDefault();
          const filter = btn.getAttribute('data-filter') || 'all';

          filterBtns.forEach(b => {
            b.classList.remove('active');
            b.style.background = 'transparent';
            b.style.color = '#333';
            b.style.borderColor = '#ccc';
          });

          btn.classList.add('active');
          btn.style.background = '#0066CC';
          btn.style.color = '#ffffff';
          btn.style.borderColor = '#0066CC';

          let countVisible = 0;
          serviceCards.forEach(card => {
            const cardCatStr = card.getAttribute('data-category') || 'all';
            const categories = cardCatStr.split(/\s+/);
            
            if (filter === 'all' || categories.includes(filter)) {
              card.style.display = 'flex';
              card.style.visibility = 'visible';
              card.style.opacity = '1';
              countVisible++;
            } else {
              card.style.display = 'none';
            }
          });

          // Xử lý hiển thị thông báo empty state khi danh mục chưa có sản phẩm
          const servicesGrid = document.getElementById('servicesGrid');
          let emptyEl = document.getElementById('filterEmptyState');
          const filterTitle = btn.textContent.trim();

          if (countVisible === 0 && servicesGrid) {
            if (!emptyEl) {
              emptyEl = document.createElement('div');
              emptyEl.id = 'filterEmptyState';
              emptyEl.style.cssText = 'grid-column: 1 / -1; padding: 48px 24px; background: linear-gradient(135deg, #F8FAFC, #EDF2F7); border: 2px dashed #CBD5E1; border-radius: 20px; text-align: center; margin: 10px 0; box-shadow: 0 10px 25px rgba(0,0,0,0.03);';
              servicesGrid.appendChild(emptyEl);
            }
            emptyEl.style.display = 'block';
            emptyEl.innerHTML = `
              <div style="width: 76px; height: 76px; margin: 0 auto 20px; border-radius: 50%; background: linear-gradient(135deg, #0066CC, #00AAFF); color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 30px; box-shadow: 0 12px 28px rgba(0,102,204,0.35); border: 4px solid #ffffff;">
                <i class="fa-solid fa-layer-group"></i>
              </div>
              <h3 style="font-size: 1.25rem; font-weight: 800; color: #0F172A; margin-bottom: 8px;">Danh mục "${filterTitle}" đang được cập nhật giải pháp mới</h3>
              <p style="font-size: 0.95rem; color: #64748B; max-width: 540px; margin: 0 auto 24px; line-height: 1.6;">
                Các dịch vụ thuộc danh mục này đang được VNPT chuẩn bị hạ tầng và sẽ bàn giao tới Quý khách trong thời gian sớm nhất. Quý khách có thể yêu cầu tư vấn gói giải pháp thiết kế riêng!
              </p>
              <button type="button" class="btn-primary btn-open-consultation" onclick="if(window.openConsultation) window.openConsultation();" style="padding: 12px 28px; border-radius: 12px; font-weight: 700; background: linear-gradient(135deg, #0066CC, #00AAFF); color: white; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 15px rgba(0,102,204,0.3);">
                <i class="fa-solid fa-headset"></i> Đăng ký tư vấn giải pháp riêng
              </button>
            `;
          } else {
            if (emptyEl) emptyEl.style.display = 'none';
          }
        });
      });
    }

    /* ---- Scroll to Top Button Logic ---- */
    const scrollToTopBtn = document.getElementById('scrollToTopBtn');
    if (scrollToTopBtn) {
      const handleScrollTop = () => {
        if (window.scrollY > 50) {
          scrollToTopBtn.classList.add('show');
        } else {
          scrollToTopBtn.classList.remove('show');
        }
      };
      window.addEventListener('scroll', handleScrollTop, { passive: true });
      handleScrollTop();

      scrollToTopBtn.onclick = function (e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      };
    }

    /* ---- Product Detail Modal Logic ---- */
    const modalOverlay = document.getElementById('modalOverlay');

    /* ---- Demo Video Modal Logic ---- */
    const openDemoVideoBtn = document.getElementById('openDemoVideoBtn');
    const demoVideoModal = document.getElementById('demoVideoModal');
    const demoVideoPlayer = document.getElementById('demoVideoPlayer');
    const closeDemoVideoModal = document.getElementById('closeDemoVideoModal');
    const closeDemoVideoBtn = document.getElementById('closeDemoVideoBtn');

    function openDemoVideo() {
      if (!demoVideoModal) return;
      if (typeof closeAllModals === 'function') closeAllModals();
      demoVideoModal.style.display = 'flex';
      demoVideoModal.classList.add('open');
      if (modalOverlay) modalOverlay.classList.add('active');

      if (demoVideoPlayer) {
        demoVideoPlayer.onerror = function() {
          const src = demoVideoPlayer.querySelector('source');
          if (src && !src.getAttribute('data-fallback')) {
            src.setAttribute('data-fallback', 'true');
            src.src = 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/ForBiggerBlazes.mp4';
            demoVideoPlayer.load();
            demoVideoPlayer.play().catch(() => {});
          }
        };
        demoVideoPlayer.play().catch(() => {});
      }
    }

    function closeDemoVideo() {
      if (!demoVideoModal) return;
      demoVideoModal.style.display = 'none';
      demoVideoModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');

      if (demoVideoPlayer) {
        demoVideoPlayer.pause();
      }
    }

    openDemoVideoBtn?.addEventListener('click', (e) => {
      e.preventDefault();
      openDemoVideo();
    });
    closeDemoVideoModal?.addEventListener('click', closeDemoVideo);
    closeDemoVideoBtn?.addEventListener('click', closeDemoVideo);

    /* ---- 3D Ecosystem Portal Modal Logic ---- */
    const heroVisual = document.querySelector('.hero-visual');
    const heroCenterIcon = document.querySelector('.hero-center-icon');
    const hero3DModal = document.getElementById('hero3DModal');
    const close3DModal = document.getElementById('close3DModal');
    const close3DBtn = document.getElementById('close3DBtn');
    const stage3DContainer = document.getElementById('stage3DContainer');
    const stage3DRig = document.getElementById('stage3DRig');
    const info3DTitle = document.getElementById('info3DTitle');
    const info3DDesc = document.getElementById('info3DDesc');

    function open3DPortal() {
      if (!hero3DModal) return;
      if (typeof closeAllModals === 'function') closeAllModals();
      hero3DModal.style.display = 'flex';
      hero3DModal.classList.add('open');
      if (modalOverlay) modalOverlay.classList.add('active');
    }

    function close3DPortal() {
      if (!hero3DModal) return;
      hero3DModal.style.display = 'none';
      hero3DModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    // Open 3D modal when clicking hero center icon or hero visual orbit
    heroCenterIcon?.addEventListener('click', (e) => {
      e.stopPropagation();
      open3DPortal();
    });
    heroVisual?.addEventListener('click', () => {
      open3DPortal();
    });

    close3DModal?.addEventListener('click', close3DPortal);
    close3DBtn?.addEventListener('click', close3DPortal);

    // 3D Spatial Tilt Control on Mouse Move inside stage3DContainer
    if (stage3DContainer && stage3DRig) {
      stage3DContainer.addEventListener('mousemove', (e) => {
        const rect = stage3DContainer.getBoundingClientRect();
        const x = e.clientX - rect.left - rect.width / 2;
        const y = e.clientY - rect.top - rect.height / 2;
        const rotX = (-y / rect.height) * 35;
        const rotY = (x / rect.width) * 35;
        stage3DRig.style.transform = `rotateX(${rotX}deg) rotateY(${rotY}deg)`;
      });

      stage3DContainer.addEventListener('mouseleave', () => {
        stage3DRig.style.transform = `rotateX(0deg) rotateY(0deg)`;
      });
    }

    // Interactive tooltip update when hovering 3D nodes
    document.querySelectorAll('.node3d').forEach(node => {
      node.addEventListener('mouseenter', () => {
        const title = node.getAttribute('data-title');
        const desc = node.getAttribute('data-desc');
        if (info3DTitle && title) info3DTitle.textContent = title;
        if (info3DDesc && desc) info3DDesc.textContent = desc;
      });
    });

    /* ---- Animated Counter (CountUp) ---- */
    const counters = document.querySelectorAll('.counter');
    let animated = false;

    function runCounters() {
      if (animated) return;
      animated = true;

      counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target') || '0', 10);
        const suffix = counter.getAttribute('data-suffix') || '';
        const duration = 2000;
        const startTime = performance.now();

        function updateCount(currentTime) {
          const elapsed = currentTime - startTime;
          const progress = Math.min(elapsed / duration, 1);
          // EaseOutQuart easing
          const easeProgress = 1 - Math.pow(1 - progress, 4);
          const currentVal = Math.floor(easeProgress * target);

          const formatted = new Intl.NumberFormat('vi-VN').format(currentVal);
          counter.textContent = formatted + suffix;

          if (progress < 1) {
            requestAnimationFrame(updateCount);
          } else {
            counter.textContent = new Intl.NumberFormat('vi-VN').format(target) + suffix;
          }
        }

        requestAnimationFrame(updateCount);
      });
    }

    const statsSection = document.getElementById('stats');
    if (statsSection && 'IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            runCounters();
          }
        });
      }, { threshold: 0.2 });
      observer.observe(statsSection);
    } else {
      runCounters();
    }

    /* ---- Stat Detail Modal Logic ---- */
    const statDetailModal = document.getElementById('statDetailModal');
    const closeStatModal = document.getElementById('closeStatModal');
    const closeStatBtn = document.getElementById('closeStatBtn');
    const statModalTitle = document.getElementById('statModalTitle');
    const statModalSub = document.getElementById('statModalSub');
    const statModalBody = document.getElementById('statModalBody');
    const statModalIcon = document.getElementById('statModalIcon');

    const statDetailsData = {
      customers: {
        title: '500.000+ Khách Hàng Doanh Nghiệp & Tập Đoàn',
        sub: 'Đối tác chiến lược đồng hành cùng VNPT & VNPT trên hành trình chuyển đổi số',
        icon: 'users',
        color: '#0066CC',
        html: `
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 16px; border-radius: 14px; text-align: center;">
              <div style="font-size: 1.8rem; font-weight: 800; color: #0066CC;">98.6%</div>
              <div style="font-size: 0.82rem; color: #64748B; font-weight: 600;">Hài lòng chất lượng dịch vụ</div>
            </div>
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 16px; border-radius: 14px; text-align: center;">
              <div style="font-size: 1.8rem; font-weight: 800; color: #00AA55;">99.99%</div>
              <div style="font-size: 0.82rem; color: #64748B; font-weight: 600;">Cam kết thời gian Uptime SLA</div>
            </div>
          </div>

          <h4 style="font-size: 0.95rem; font-weight: 800; color: #0F172A; margin-bottom: 12px;">Các Tập Đoàn & Doanh Nghiệp Tiêu Biểu:</h4>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-bottom: 20px;">
            <div style="background: #F1F5F9; padding: 10px; border-radius: 10px; text-align: center; font-weight: 700; font-size: 0.85rem; color: #334155;">Vinamilk</div>
            <div style="background: #F1F5F9; padding: 10px; border-radius: 10px; text-align: center; font-weight: 700; font-size: 0.85rem; color: #334155;">Vietcombank</div>
            <div style="background: #F1F5F9; padding: 10px; border-radius: 10px; text-align: center; font-weight: 700; font-size: 0.85rem; color: #334155;">PetroVietnam</div>
            <div style="background: #F1F5F9; padding: 10px; border-radius: 10px; text-align: center; font-weight: 700; font-size: 0.85rem; color: #334155;">TH True Milk</div>
            <div style="background: #F1F5F9; padding: 10px; border-radius: 10px; text-align: center; font-weight: 700; font-size: 0.85rem; color: #334155;">Vietnam Airlines</div>
            <div style="background: #F1F5F9; padding: 10px; border-radius: 10px; text-align: center; font-weight: 700; font-size: 0.85rem; color: #334155;">Masan Group</div>
          </div>

          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; font-size: 0.88rem; color: #475569;">
            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="check-circle" style="width: 16px; height: 16px; color: #0066CC;"></i> Phục vụ hơn 500.000 doanh nghiệp vừa và nhỏ (SME) toàn quốc</li>
            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="check-circle" style="width: 16px; height: 16px; color: #0066CC;"></i> 100% Bộ Ngành và cơ quan hành chính nhà nước sử dụng hạ tầng VNPT</li>
          </ul>
        `
      },
      servers: {
        title: '10.000+ Máy Chủ Data Center Tier III International',
        sub: 'Hạ tầng Trung tâm Dữ liệu đạt chứng nhận Quốc tế lớn nhất Việt Nam',
        icon: 'server',
        color: '#00AAFF',
        html: `
          <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 18px; border-radius: 16px; margin-bottom: 20px;">
            <div style="font-weight: 800; font-size: 1.05rem; color: #0F172A; margin-bottom: 8px;">Hệ Thống Data Center Siêu Quy Mô:</div>
            <p style="font-size: 0.88rem; color: #64748B; margin: 0 0 12px; line-height: 1.5;">Hệ thống Trung tâm Dữ liệu VNPT Tân Thuận (TP.HCM), Hòa Lạc (Hà Nội) &amp; Cần Thơ đạt tiêu chuẩn quốc tế ANSI/TIA-942-B Rated 3 Construct.</p>
            <div style="display: flex; gap: 16px; flex-wrap: wrap;">
              <span style="background: #E0F2FE; color: #0369A1; padding: 4px 12px; border-radius: 12px; font-size: 0.78rem; font-weight: 700;">Dung lượng kết nối 100Gbps+</span>
              <span style="background: #DCFCE7; color: #15803D; padding: 4px 12px; border-radius: 12px; font-size: 0.78rem; font-weight: 700;">Chống Anti-DDoS 24/7</span>
              <span style="background: #FEF3C7; color: #B45309; padding: 4px 12px; border-radius: 12px; font-size: 0.78rem; font-weight: 700;">ISO/IEC 27001 &amp; ISO 9001</span>
            </div>
          </div>

          <h4 style="font-size: 0.95rem; font-weight: 800; color: #0F172A; margin-bottom: 10px;">Thông Số Kỹ Thuật Đỉnh Cao:</h4>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.88rem; color: #475569;">
            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="cpu" style="width: 16px; height: 16px; color: #00AAFF;"></i> Hệ thống máy chủ Chipset Intel Xeon Platinum &amp; NVIDIA AI Accelerator</li>
            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="hard-drive" style="width: 16px; height: 16px; color: #00AAFF;"></i> Lưu trữ SSD NVMe Enterprise siêu tốc độ với khả năng Backup dữ liệu đa vùng</li>
            <li style="display: flex; align-items: center; gap: 8px;"><i data-lucide="shield" style="width: 16px; height: 16px; color: #00AAFF;"></i> Tự động khôi phục sự cố Disaster Recovery (DR) dưới 60 giây</li>
          </ul>
        `
      },
      provinces: {
        title: 'Bản Đồ Phủ Sóng 63/63 Tỉnh Thành Toàn Quốc',
        sub: 'Mạng lưới hạ tầng viễn thông và cáp quang trải dài từ thành thị đến hải đảo',
        icon: 'globe',
        color: '#FFCC00',
        html: `
          <div style="background: #FEFCE8; border: 1px solid #FEF08A; padding: 18px; border-radius: 16px; margin-bottom: 20px; color: #854D0E;">
            <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px;">Hạ Tầng Mạng Quốc Gia VNPT:</div>
            <p style="font-size: 0.88rem; margin: 0; line-height: 1.5;">Hơn 100.000+ km cáp quang trục Bắc - Nam, kết nối 100% các xã phường, huyện đảo và vùng sâu vùng xa trên toàn lãnh thổ Việt Nam.</p>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 20px; text-align: center;">
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 12px; border-radius: 12px;">
              <div style="font-size: 1.3rem; font-weight: 800; color: #0066CC;">100.000+</div>
              <div style="font-size: 0.75rem; color: #64748B;">Km cáp quang trục</div>
            </div>
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 12px; border-radius: 12px;">
              <div style="font-size: 1.3rem; font-weight: 800; color: #0066CC;">63/63</div>
              <div style="font-size: 0.75rem; color: #64748B;">Tỉnh thành phủ 5G</div>
            </div>
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 12px; border-radius: 12px;">
              <div style="font-size: 1.3rem; font-weight: 800; color: #0066CC;">24/7/365</div>
              <div style="font-size: 0.75rem; color: #64748B;">Trạm điều hành mạng</div>
            </div>
          </div>
        `
      },
      awards: {
        title: '150+ Giải Thưởng Công Nghệ Trong & Ngoài Nước',
        sub: 'Ghi nhận uy tín thương hiệu và đóng góp tiên phong trong chuyển đổi số quốc gia',
        icon: 'award',
        color: '#FF6B00',
        html: `
          <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 16px;">
            <div style="background: #FFF7ED; border: 1px solid #FFEDD5; padding: 14px 18px; border-radius: 14px; display: flex; align-items: center; gap: 14px;">
              <div style="width: 40px; height: 40px; border-radius: 50%; background: #FF6B00; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0;">★</div>
              <div>
                <strong style="font-size: 0.95rem; color: #9A3412; display: block;">Stevie Awards International 2024</strong>
                <span style="font-size: 0.82rem; color: #C2410C;">Giải Vàng Nền tảng Chuyển đổi số xuất sắc nhất Châu Á - Thái Bình Dương</span>
              </div>
            </div>

            <div style="background: #FFF7ED; border: 1px solid #FFEDD5; padding: 14px 18px; border-radius: 14px; display: flex; align-items: center; gap: 14px;">
              <div style="width: 40px; height: 40px; border-radius: 50%; background: #0066CC; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0;">★</div>
              <div>
                <strong style="font-size: 0.95rem; color: #1E40AF; display: block;">Make in Viet Nam Gold 2023 &amp; 2024</strong>
                <span style="font-size: 0.82rem; color: #1D4ED8;">Giải thưởng sản phẩm Công nghệ số xuất sắc cho Doanh nghiệp</span>
              </div>
            </div>

            <div style="background: #FFF7ED; border: 1px solid #FFEDD5; padding: 14px 18px; border-radius: 14px; display: flex; align-items: center; gap: 14px;">
              <div style="width: 40px; height: 40px; border-radius: 50%; background: #00AA55; color: white; display: flex; align-items: center; justify-content: center; font-weight: 800; flex-shrink: 0;">★</div>
              <div>
                <strong style="font-size: 0.95rem; color: #166534; display: block;">Sao Khuê Top 10 ICT 10 Năm Liên Tiếp</strong>
                <span style="font-size: 0.82rem; color: #15803D;">Danh hiệu danh giá dành cho Nền tảng Cloud &amp; AI Hạ tầng số VNPT</span>
              </div>
            </div>
          </div>
        `
      }
    };

    function openStatModal(type) {
      if (!statDetailModal || !statDetailsData[type]) return;
      const data = statDetailsData[type];

      if (statModalTitle) statModalTitle.textContent = data.title;
      if (statModalSub) statModalSub.textContent = data.sub;
      if (statModalBody) statModalBody.innerHTML = data.html;
      if (statModalIcon) statModalIcon.setAttribute('data-lucide', data.icon);

      if (window.lucide) window.lucide.createIcons();

      if (typeof closeAllModals === 'function') closeAllModals();
      statDetailModal.style.display = 'flex';
      statDetailModal.classList.add('open');
      if (modalOverlay) modalOverlay.classList.add('active');
    }

    function closeStatDetailModal() {
      if (!statDetailModal) return;
      statDetailModal.style.display = 'none';
      statDetailModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    document.querySelectorAll('.clickable-stat').forEach(card => {
      card.addEventListener('click', () => {
        const type = card.getAttribute('data-type');
        openStatModal(type);
      });
    });

    closeStatModal?.addEventListener('click', closeStatDetailModal);
    closeStatBtn?.addEventListener('click', closeStatDetailModal);

    /* ---- Quick Consultation Modal Logic ---- */
    const openConsultationBtn = document.getElementById('openConsultationBtn');
    const consultationModal = document.getElementById('consultationModal');
    const closeConsultationModal = document.getElementById('closeConsultationModal');
    const consultationForm = document.getElementById('consultationForm');

    window.openConsultation = function() {
      const cModal = document.getElementById('consultationModal');
      const mOverlay = document.getElementById('modalOverlay');
      if (typeof closeAllModals === 'function') closeAllModals();

      if (cModal) {
        cModal.style.display = 'flex';
        cModal.classList.add('open');
        if (mOverlay) mOverlay.classList.add('active');
        return;
      }

      // Cuộn mượt xuống form đăng ký tư vấn nếu không có modal
      const contactSec = document.getElementById('contact');
      if (contactSec) {
        const top = contactSec.getBoundingClientRect().top + window.scrollY - 100;
        window.scrollTo({ top, behavior: 'smooth' });
      }
    };

    function closeConsultation() {
      if (!consultationModal) return;
      consultationModal.style.display = 'none';
      consultationModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    openConsultationBtn?.addEventListener('click', (e) => {
      e.preventDefault();
      window.openConsultation();
    });

    closeConsultationModal?.addEventListener('click', closeConsultation);

    consultationForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('consultName')?.value.trim();
      const phone = document.getElementById('consultPhone')?.value.trim();
      const email = document.getElementById('consultEmail')?.value.trim();
      const service = document.getElementById('consultService')?.value;
      const message = document.getElementById('consultMessage')?.value.trim();

      if (!name || !phone) return;

      const formData = new FormData();
      formData.append('name', name);
      formData.append('phone', phone);
      formData.append('email', email || '');
      formData.append('service', service || 'dich_vu_so');
      formData.append('message', message || '');

      closeConsultation();

      try {
        const res = await fetch('../backend/api/consultation.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toastMsg');
        if (toast && toastMsg) {
          toastMsg.textContent = data.message || `Cảm ơn ${name}! VNPT đã ghi nhận yêu cầu tư vấn và sẽ phản hồi cho bạn trong thời gian sớm nhất!`;
          toast.classList.add('show');
          setTimeout(() => toast.classList.remove('show'), 5000);
        }
      } catch (err) {
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toastMsg');
        if (toast && toastMsg) {
          toastMsg.textContent = `Cảm ơn ${name}! VNPT đã ghi nhận yêu cầu tư vấn và sẽ phản hồi cho bạn trong thời gian sớm nhất!`;
          toast.classList.add('show');
          setTimeout(() => toast.classList.remove('show'), 5000);
        }
      }
    });

    /* ---- Bottom Contact Form Submission Logic ---- */
    const contactForm = document.getElementById('contactForm');
    const formSuccess = document.getElementById('formSuccess');

    contactForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('bottomContactName')?.value.trim();
      const phone = document.getElementById('bottomContactPhone')?.value.trim();
      const email = document.getElementById('bottomContactEmail')?.value.trim();
      const service = document.getElementById('bottomContactService')?.value;
      const message = document.getElementById('bottomContactMessage')?.value.trim();

      if (!name || !phone) return;

      const formData = new FormData();
      formData.append('name', name);
      formData.append('phone', phone);
      formData.append('email', email || '');
      formData.append('service', service || 'chuyen_doi_so');
      formData.append('message', message || '');

      try {
        const res = await fetch('../backend/api/consultation.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        if (formSuccess) {
          formSuccess.style.display = 'flex';
        }

        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toastMsg');
        if (toast && toastMsg) {
          toastMsg.textContent = data.message || `Cảm ơn ${name}! VNPT đã ghi nhận yêu cầu tư vấn và sẽ phản hồi cho bạn trong thời gian sớm nhất!`;
          toast.classList.add('show');
          setTimeout(() => toast.classList.remove('show'), 5000);
        }

        contactForm.reset();
      } catch (err) {
        if (formSuccess) {
          formSuccess.style.display = 'flex';
        }
        contactForm.reset();
      }
    });

    /* ---- Footer Interactive Nav & Modals Logic ---- */
    // 1. Footer Service Filter Links (.footer-cat-link)
    document.querySelectorAll('.footer-cat-link').forEach(link => {
      link.addEventListener('click', (e) => {
        const cat = link.getAttribute('data-cat');
        const targetBtn = document.querySelector(`.filter-btn[data-filter="${cat}"]`);
        if (targetBtn) {
          targetBtn.click();
        }
      });
    });

    // 2. Footer Solution Links (.footer-stat-link)
    document.querySelectorAll('.footer-stat-link').forEach(link => {
      link.addEventListener('click', (e) => {
        const type = link.getAttribute('data-type');
        if (typeof openStatModal === 'function' && type) {
          openStatModal(type);
        }
      });
    });

    // 3. Technical Docs Modal (#techDocsModal)
    const openTechDocsLink = document.getElementById('openTechDocsLink');
    const techDocsModal = document.getElementById('techDocsModal');
    const closeTechDocsModal = document.getElementById('closeTechDocsModal');
    const closeTechDocsBtn = document.getElementById('closeTechDocsBtn');

    function openTechDocs() {
      if (!techDocsModal) return;
      if (typeof closeAllModals === 'function') closeAllModals();
      techDocsModal.style.display = 'flex';
      techDocsModal.classList.add('open');
      if (modalOverlay) modalOverlay.classList.add('active');
    }

    function closeTechDocs() {
      if (!techDocsModal) return;
      techDocsModal.style.display = 'none';
      techDocsModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    openTechDocsLink?.addEventListener('click', (e) => {
      e.preventDefault();
      openTechDocs();
    });
    closeTechDocsModal?.addEventListener('click', closeTechDocs);
    closeTechDocsBtn?.addEventListener('click', closeTechDocs);

    // 4. Support Center Link (#openSupportCenterLink -> Trigger Live Chat or Support Modal)
    const openSupportCenterLink = document.getElementById('openSupportCenterLink');
    openSupportCenterLink?.addEventListener('click', (e) => {
      e.preventDefault();
      if (typeof openSupportCenterModalFunc === 'function') {
        openSupportCenterModalFunc();
      }
    });

    /* ---- Solution Showcase Modal Logic (#solutionModal) ---- */
    const solutionModal = document.getElementById('solutionModal');
    const closeSolutionModal = document.getElementById('closeSolutionModal');
    const closeSolutionBtn = document.getElementById('closeSolutionBtn');
    const solutionConsultBtn = document.getElementById('solutionConsultBtn');
    const solutionModalTitle = document.getElementById('solutionModalTitle');
    const solutionModalSub = document.getElementById('solutionModalSub');
    const solutionModalBody = document.getElementById('solutionModalBody');
    const solutionModalIcon = document.getElementById('solutionModalIcon');

    const solutionsData = {
      sme: {
        title: 'Giải Pháp Chuyển Đổi Số Cho Doanh Nghiệp SME',
        sub: 'Gói giải pháp nhỏ gọn, tiết kiệm chi phí tối đa cho doanh nghiệp vừa và nhỏ',
        icon: 'briefcase',
        html: `
          <div style="background: #F0F9FF; border: 1px solid #BAE6FD; padding: 18px; border-radius: 16px; margin-bottom: 18px; color: #0369A1;">
            <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px;">🚀 Trọn Bộ Giải Pháp SME VNPT 4-in-1:</div>
            <p style="font-size: 0.88rem; margin: 0; line-height: 1.5;">Bao gồm: Chữ ký số VNPT SmartCA + Hóa đơn điện tử VNPT Invoice + Internet Cáp quang FiberVNN + Cloud Server giá ưu đãi.</p>
          </div>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: #334155;">
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="check-circle" style="color: #0066CC; width: 18px; height: 18px;"></i> Giảm ngay 35% chi phí vận hành doanh nghiệp năm đầu tiên</li>
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="check-circle" style="color: #0066CC; width: 18px; height: 18px;"></i> Khai thuế, nộp BHXH &amp; phát hành hóa đơn điện tử không cần USB Token</li>
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="check-circle" style="color: #0066CC; width: 18px; height: 18px;"></i> Triển khai nhanh chóng chỉ trong 24 giờ đăng ký</li>
          </ul>
        `
      },
      enterprise: {
        title: 'Giải Pháp Hạ Tầng Số Cho Tập Đoàn Lớn & Tổng Công Ty',
        sub: 'Hạ tầng Cloud siêu quy mô, bảo mật chuẩn quân sự ISO 27001',
        icon: 'building-2',
        html: `
          <div style="background: #F0FDF4; border: 1px solid #BBF7D0; padding: 18px; border-radius: 16px; margin-bottom: 18px; color: #166534;">
            <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px;">🏢 Hạ Tầng Siêu Quy Mô Dedicated Cloud:</div>
            <p style="font-size: 0.88rem; margin: 0; line-height: 1.5;">Hệ thống Máy chủ ảo dùng riêng (Private Cloud), Kênh truyền dẫn leased-line đường truyền riêng 10Gbps+ và Trung tâm sao lưu Disaster Recovery (DR).</p>
          </div>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: #334155;">
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="shield-check" style="color: #00AA55; width: 18px; height: 18px;"></i> Cam kết thời gian đáp ứng sự cố dưới 15 phút (24/7/365)</li>
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="shield-check" style="color: #00AA55; width: 18px; height: 18px;"></i> Giám sát an ninh mạng thời gian thực chống DDoS và Ransomware</li>
          </ul>
        `
      },
      gov: {
        title: 'Giải Pháp Chính Phủ Số & Đô Thị Thông Minh (Smart City)',
        sub: 'Đồng hành cùng Chương trình Chuyển đổi số Quốc gia',
        icon: 'landmark',
        html: `
          <div style="background: #FEFCE8; border: 1px solid #FEF08A; padding: 18px; border-radius: 16px; margin-bottom: 18px; color: #854D0E;">
            <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px;">🏛️ Nền Tảng Điều Hành Đô Thị Thông Minh IOC:</div>
            <p style="font-size: 0.88rem; margin: 0; line-height: 1.5;">Hệ thống Văn phòng điện tử VNPT iOffice, Cổng dịch vụ công trực tuyến và Trung tâm IOC giám sát điều hành đô thị thời gian thực.</p>
          </div>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: #334155;">
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="check-circle" style="color: #D97706; width: 18px; height: 18px;"></i> Đã triển khai thành công tại 55+ Tỉnh/Thành phố trên cả nước</li>
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="check-circle" style="color: #D97706; width: 18px; height: 18px;"></i> Tiết kiệm 70% thời gian xử lý hồ sơ hành chính công</li>
          </ul>
        `
      },
      health: {
        title: 'Giải Pháp Y Tế Số & Bệnh Viện Thông Minh (VNPT HIS)',
        sub: 'Hệ thống quản lý thông tin bệnh viện &amp; Hồ sơ sức khoẻ điện tử',
        icon: 'heart-pulse',
        html: `
          <div style="background: #FFF1F2; border: 1px solid #FECDD3; padding: 18px; border-radius: 16px; margin-bottom: 18px; color: #9F1239;">
            <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px;">🏥 Hệ Thống Y Tế Số VNPT HIS &amp; Telehealth:</div>
            <p style="font-size: 0.88rem; margin: 0; line-height: 1.5;">Bệnh án điện tử EMR, Khám chữa bệnh từ xa Telemedicine và kết nối Cổng Dược quốc gia.</p>
          </div>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: #334155;">
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="check-circle" style="color: #E11D48; width: 18px; height: 18px;"></i> Kết nối hơn 7.000 Cơ sở y tế &amp; Bệnh viện toàn quốc</li>
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="check-circle" style="color: #E11D48; width: 18px; height: 18px;"></i> Đặt lịch khám &amp; thanh toán không dùng tiền mặt qua App</li>
          </ul>
        `
      },
      edu: {
        title: 'Giải Pháp Giáo Dục Số (VNPT edu)',
        sub: 'Hệ sinh thái Quản lý Học tập &amp; Trường học Thông minh',
        icon: 'graduation-cap',
        html: `
          <div style="background: #F0FDF4; border: 1px solid #BBF7D0; padding: 18px; border-radius: 16px; margin-bottom: 18px; color: #166534;">
            <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px;">🎓 Nền Tảng Giáo Dục VNPT edu:</div>
            <p style="font-size: 0.88rem; margin: 0; line-height: 1.5;">Mạng Giáo dục Việt Nam VNPT edu kết nối Nhà trường - Giáo viên - Phụ huynh - Học sinh.</p>
          </div>
          <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: #334155;">
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="check-circle" style="color: #16A34A; width: 18px; height: 18px;"></i> Hơn 9 triệu hồ sơ học sinh &amp; 30.000 trường học tin dùng</li>
            <li style="display: flex; align-items: center; gap: 10px;"><i data-lucide="check-circle" style="color: #16A34A; width: 18px; height: 18px;"></i> Sổ liên lạc điện tử, Học bạ số &amp; Thi trực tuyến chuẩn BGD</li>
          </ul>
        `
      }
    };

    function openSolutionModal(type) {
      if (!solutionModal || !solutionsData[type]) return;
      const data = solutionsData[type];

      if (solutionModalTitle) solutionModalTitle.textContent = data.title;
      if (solutionModalSub) solutionModalSub.textContent = data.sub;
      if (solutionModalBody) solutionModalBody.innerHTML = data.html;
      if (solutionModalIcon) solutionModalIcon.setAttribute('data-lucide', data.icon);

      if (window.lucide) window.lucide.createIcons();

      if (typeof closeAllModals === 'function') closeAllModals();
      solutionModal.style.display = 'flex';
      solutionModal.classList.add('open');
      if (modalOverlay) modalOverlay.classList.add('active');
    }

    function closeSolutionModalFunc() {
      if (!solutionModal) return;
      solutionModal.style.display = 'none';
      solutionModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    // Attach click listeners to Solution links
    document.querySelectorAll('.footer-col h4').forEach(h4 => {
      const titleText = h4.textContent.trim();
      const ul = h4.nextElementSibling;
      if (!ul) return;

      if (titleText.indexOf('Giải pháp') !== -1) {
        const lis = ul.querySelectorAll('li a');
        const solutionKeys = ['sme', 'enterprise', 'gov', 'health', 'edu'];
        lis.forEach((a, idx) => {
          a.addEventListener('click', (e) => {
            e.preventDefault();
            const key = solutionKeys[idx] || 'sme';
            openSolutionModal(key);
          });
        });
      }
    });

    closeSolutionModal?.addEventListener('click', closeSolutionModalFunc);
    closeSolutionBtn?.addEventListener('click', closeSolutionModalFunc);

    solutionConsultBtn?.addEventListener('click', () => {
      closeSolutionModalFunc();
      if (typeof openConsultation === 'function') {
        openConsultation();
      }
    });

    // Support Center Modal Trigger
    const supportCenterModal = document.getElementById('supportCenterModal');
    const closeSupportCenterModal = document.getElementById('closeSupportCenterModal');
    const closeSupportCenterBtn = document.getElementById('closeSupportCenterBtn');

    function openSupportCenterModalFunc() {
      if (!supportCenterModal) return;
      if (typeof closeAllModals === 'function') closeAllModals();
      supportCenterModal.style.display = 'flex';
      supportCenterModal.classList.add('open');
      if (modalOverlay) modalOverlay.classList.add('active');
    }

    function closeSupportCenterModalFunc() {
      if (!supportCenterModal) return;
      supportCenterModal.style.display = 'none';
      supportCenterModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    closeSupportCenterModal?.addEventListener('click', closeSupportCenterModalFunc);
    closeSupportCenterBtn?.addEventListener('click', closeSupportCenterModalFunc);

    // Hòm Thư Tra Cứu Phản Hồi Tư Vấn Khách Hàng (#checkSupportModal)
    const checkSupportModal = document.getElementById('checkSupportModal');
    const openCheckSupportBtn = document.getElementById('openCheckSupportBtn');
    const userCheckSupportLink = document.getElementById('userCheckSupportLink');
    const closeCheckSupportModal = document.getElementById('closeCheckSupportModal');
    const btnDoCheckSupport = document.getElementById('btnDoCheckSupport');
    const checkSupportQueryInput = document.getElementById('checkSupportQueryInput');
    const checkSupportResultsList = document.getElementById('checkSupportResultsList');

    function openCheckSupportModalFunc(defaultQuery = '') {
      if (!checkSupportModal) return;
      if (typeof closeAllModals === 'function') closeAllModals();
      checkSupportModal.style.display = 'flex';
      checkSupportModal.classList.add('open');

      if (defaultQuery && checkSupportQueryInput) {
        checkSupportQueryInput.value = defaultQuery;
        doFetchSupportStatus(defaultQuery);
      } else {
        // Auto check if customer logged in
        const savedUserStr = localStorage.getItem('vnpt_user');
        if (savedUserStr) {
          try {
            const u = JSON.parse(savedUserStr);
            const query = u.phone || u.so_dien_thoai || u.email || '';
            if (query && checkSupportQueryInput && !checkSupportQueryInput.value) {
              checkSupportQueryInput.value = query;
              doFetchSupportStatus(query);
            }
          } catch (_e) {}
        }
      }
    }

    function closeCheckSupportModalFunc() {
      if (!checkSupportModal) return;
      checkSupportModal.style.display = 'none';
      checkSupportModal.classList.remove('open');
    }

    function doFetchSupportStatus(query) {
      if (!query || !query.trim()) {
        if (checkSupportResultsList) {
          checkSupportResultsList.innerHTML = '<div style="text-align:center;color:#EF4444;padding:20px;">Vui lòng nhập Email hoặc Số điện thoại để tra cứu!</div>';
        }
        return;
      }

      if (checkSupportResultsList) {
        checkSupportResultsList.innerHTML = '<div style="text-align:center;color:#64748B;padding:30px;"><i class="fa-solid fa-spinner fa-spin fa-2x mb-2"></i><p>Đang tìm câu trả lời...</p></div>';
      }

      fetch('../backend/api/check_support_status.php?query=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
          if (!checkSupportResultsList) return;
          if (data.status === 'success' && data.data && data.data.length > 0) {
            let html = '';
            data.data.forEach(item => {
              const statusBadgeMap = {
                'moi': '<span style="background:#E0F2FE;color:#0369A1;padding:3px 10px;border-radius:12px;font-size:0.75rem;font-weight:700;">🕒 Đang chờ tiếp nhận</span>',
                'dang_xu_ly': '<span style="background:#FEF3C7;color:#D97706;padding:3px 10px;border-radius:12px;font-size:0.75rem;font-weight:700;">⚙️ Đang xử lý</span>',
                'da_giai_quyet': '<span style="background:#DCFCE7;color:#15803D;padding:3px 10px;border-radius:12px;font-size:0.75rem;font-weight:700;">✅ Đã phản hồi</span>',
                'da_dong': '<span style="background:#F1F5F9;color:#64748B;padding:3px 10px;border-radius:12px;font-size:0.75rem;font-weight:700;">🔒 Đã hoàn thành</span>'
              };
              const statusBadge = statusBadgeMap[item.trang_thai] || statusBadgeMap['moi'];

              let repliesHtml = '';
              if (item.replies && item.replies.length > 0) {
                repliesHtml += '<div style="margin-top:14px;padding-top:14px;border-top:1px dashed #CBD5E1;"><div style="font-weight:800;color:#0F172A;font-size:0.88rem;margin-bottom:10px;"><i class="fa-solid fa-reply-all me-1 text-primary"></i> Lịch Sử Trả Lời Từ Chuyên Viên VNPT:</div>';
                item.replies.forEach(rep => {
                  repliesHtml += `
                    <div style="background:#F0F9FF;border:1px solid #BAE6FD;border-left:4px solid #0066CC;padding:12px 16px;border-radius:0 12px 12px 0;margin-bottom:8px;">
                      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                        <strong style="color:#0369A1;font-size:0.88rem;"><i class="fa-solid fa-user-shield me-1"></i> ${rep.nhan_vien_ten || 'Chuyên viên VNPT'}</strong>
                        <span style="font-size:0.75rem;color:#64748B;">${rep.created_at || ''}</span>
                      </div>
                      <div style="color:#334155;font-size:0.92rem;line-height:1.5;">${rep.noi_dung ? rep.noi_dung.replace(/\n/g, '<br>') : ''}</div>
                    </div>
                  `;
                });
                repliesHtml += '</div>';
              } else {
                repliesHtml = '<div style="margin-top:10px;font-size:0.82rem;color:#64748B;font-style:italic;"><i class="fa-regular fa-clock me-1"></i> Yêu cầu của bạn đang được Chuyên viên VNPT xử lý, kết quả trả lời sẽ hiển thị tại đây.</div>';
              }

              html += `
                <div style="background:#F8FAFC;border:1px solid #E2E8F0;padding:18px;border-radius:16px;">
                  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px;">
                    <div style="font-weight:800;color:#0F172A;font-size:0.98rem;">📌 ${item.tieu_de || 'Yêu cầu tư vấn'}</div>
                    ${statusBadge}
                  </div>
                  <div style="background:white;border:1px solid #F1F5F9;padding:10px 14px;border-radius:10px;font-size:0.88rem;color:#475569;margin-bottom:6px;">
                    ${item.noi_dung ? item.noi_dung.replace(/\n/g, '<br>') : ''}
                  </div>
                  <div style="font-size:0.75rem;color:#94A3B8;">Thời gian gửi: ${item.created_at || ''}</div>
                  ${repliesHtml}
                </div>
              `;
            });
            checkSupportResultsList.innerHTML = html;
          } else {
            checkSupportResultsList.innerHTML = '<div style="text-align:center;color:#64748B;padding:30px;"><i class="fa-solid fa-folder-open fa-2x mb-2 text-muted"></i><p>Không tìm thấy tin nhắn tư vấn nào tương ứng với <strong>' + query + '</strong>.</p></div>';
          }
        })
        .catch(_err => {
          if (checkSupportResultsList) {
            checkSupportResultsList.innerHTML = '<div style="text-align:center;color:#EF4444;padding:20px;">Lỗi kết nối máy chủ tra cứu!</div>';
          }
        });
    }

    openCheckSupportBtn?.addEventListener('click', () => openCheckSupportModalFunc());
    userCheckSupportLink?.addEventListener('click', () => openCheckSupportModalFunc());
    closeCheckSupportModal?.addEventListener('click', () => closeCheckSupportModalFunc());
    btnDoCheckSupport?.addEventListener('click', () => {
      if (checkSupportQueryInput) doFetchSupportStatus(checkSupportQueryInput.value);
    });
    checkSupportQueryInput?.addEventListener('keyup', (e) => {
      if (e.key === 'Enter' && checkSupportQueryInput) doFetchSupportStatus(checkSupportQueryInput.value);
    });

    // Modal Thông báo từ Admin cho Khách hàng (#customerNotifModal)
    const customerNotifModal = document.getElementById('customerNotifModal');
    const openUserNotifBtn = document.getElementById('openUserNotifBtn');
    const userNotifLink = document.getElementById('userNotifLink');
    const closeCustomerNotifModal = document.getElementById('closeCustomerNotifModal');
    const customerNotifList = document.getElementById('customerNotifList');
    const headerNotifBadge = document.getElementById('headerNotifBadge');
    const userDropdownNotifBadge = document.getElementById('userDropdownNotifBadge');

    function openCustomerNotifModalFunc() {
      if (!customerNotifModal) return;
      if (typeof closeAllModals === 'function') closeAllModals();
      customerNotifModal.style.display = 'flex';
      customerNotifModal.classList.add('open');
      fetchCustomerNotifs();
    }

    function closeCustomerNotifModalFunc() {
      if (!customerNotifModal) return;
      customerNotifModal.style.display = 'none';
      customerNotifModal.classList.remove('open');
    }

    function fetchCustomerNotifs() {
      let queryStr = '';
      if (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function') {
        const u = window.VNPTAuth.getCurrentUser();
        if (u) {
          if (u.email) queryStr += '&email=' + encodeURIComponent(u.email);
          if (u.phone || u.so_dien_thoai) queryStr += '&phone=' + encodeURIComponent(u.phone || u.so_dien_thoai);
        }
      }
      if (!queryStr) {
        const savedStr = localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user') || localStorage.getItem('vnpt_user');
        if (savedStr) {
          try {
            const u = JSON.parse(savedStr);
            if (u.email) queryStr += '&email=' + encodeURIComponent(u.email);
            if (u.phone || u.so_dien_thoai) queryStr += '&phone=' + encodeURIComponent(u.phone || u.so_dien_thoai);
          } catch (_e) {}
        }
      }

      fetch('../backend/api/get_notifications.php?action=get' + queryStr)
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success') {
            const count = data.unread_count || 0;
            if (headerNotifBadge) {
              headerNotifBadge.textContent = count;
              headerNotifBadge.style.display = count > 0 ? 'flex' : 'none';
            }
            if (userDropdownNotifBadge) {
              userDropdownNotifBadge.textContent = count;
              userDropdownNotifBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }

            if (customerNotifList) {
              if (data.data && data.data.length > 0) {
                let html = '';
                data.data.forEach(item => {
                  const isUnread = item.da_doc == 0;
                  const iconMap = {
                    'he_thong': 'fa-circle-info text-primary',
                    'khuyen_mai': 'fa-gift text-warning',
                    'don_hang': 'fa-box text-success'
                  };
                  const iconClass = iconMap[item.loai] || 'fa-bell text-primary';

                  html += `
                    <div style="background:${isUnread ? '#F0F9FF' : '#F8FAFC'}; border:1px solid ${isUnread ? '#BAE6FD' : '#E2E8F0'}; border-left:4px solid ${isUnread ? '#0066CC' : '#94A3B8'}; padding:16px; border-radius:12px; transition:0.2s;">
                      <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:6px;">
                        <div style="font-weight:800; color:#0F172A; font-size:0.98rem; display:flex; align-items:center; gap:8px;">
                          <i class="fa-solid ${iconClass}"></i> ${item.tieu_de || 'Thông báo hệ thống'}
                        </div>
                        ${isUnread ? '<span style="background:#0066CC; color:white; font-size:0.7rem; font-weight:800; padding:2px 8px; border-radius:10px;">MỚI</span>' : '<span style="color:#94A3B8; font-size:0.75rem;">Đã đọc</span>'}
                      </div>
                      <div style="color:#334155; font-size:0.9rem; line-height:1.5; margin-bottom:8px;">
                        ${item.noi_dung ? item.noi_dung.replace(/\n/g, '<br>') : ''}
                      </div>
                      <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.75rem; color:#64748B;">
                        <span><i class="fa-regular fa-clock me-1"></i> ${item.created_at || ''}</span>
                        ${isUnread ? `<button type="button" class="btn-mark-read" data-id="${item.id}" style="background:none; border:none; color:#0066CC; font-weight:700; cursor:pointer; font-size:0.78rem;"><i class="fa-solid fa-check me-1"></i>Đánh dấu đã đọc</button>` : ''}
                      </div>
                    </div>
                  `;
                });
                customerNotifList.innerHTML = html;

                document.querySelectorAll('.btn-mark-read').forEach(btn => {
                  btn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const notifId = this.getAttribute('data-id');
                    const formData = new FormData();
                    formData.append('id', notifId);
                    fetch('../backend/api/get_notifications.php?action=mark_read', { method: 'POST', body: formData })
                      .then(() => fetchCustomerNotifs());
                  });
                });
              } else {
                customerNotifList.innerHTML = '<div style="text-align:center; color:#64748B; padding:40px 10px;"><i class="fa-solid fa-bell-slash fa-3x mb-3 text-muted"></i><p style="font-size:0.95rem; margin:0;">Bạn chưa có thông báo nào từ hệ thống.</p></div>';
              }
            }
          }
        })
        .catch(_e => {});
    }

    openUserNotifBtn?.addEventListener('click', () => openCustomerNotifModalFunc());
    userNotifLink?.addEventListener('click', () => openCustomerNotifModalFunc());
    closeCustomerNotifModal?.addEventListener('click', () => closeCustomerNotifModalFunc());

    const btnMarkAllNotifsRead = document.getElementById('btnMarkAllNotifsRead');
    btnMarkAllNotifsRead?.addEventListener('click', () => {
      let queryStr = '';
      if (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function') {
        const u = window.VNPTAuth.getCurrentUser();
        if (u && u.email) queryStr += '&email=' + encodeURIComponent(u.email);
      }
      if (!queryStr) {
        const savedStr = localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user');
        if (savedStr) {
          try {
            const u = JSON.parse(savedStr);
            if (u.email) queryStr += '&email=' + encodeURIComponent(u.email);
          } catch (_e) {}
        }
      }
      fetch('../backend/api/get_notifications.php?action=mark_all_read' + queryStr, { method: 'POST' })
        .then(res => res.json())
        .then(() => fetchCustomerNotifs())
        .catch(_e => {});
    });

    // Auto fetch notifications on load & listen for authchange
    document.addEventListener('vnpt:authchange', () => {
      const savedUserStr = localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user');
      if (savedUserStr) {
        if (openUserNotifBtn) openUserNotifBtn.style.display = 'flex';
        fetchCustomerNotifs();
      } else {
        if (openUserNotifBtn) openUserNotifBtn.style.display = 'none';
      }
    });

    // Check on initial load
    setTimeout(() => {
      const savedUserStr = localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user');
      if (savedUserStr) {
        if (openUserNotifBtn) openUserNotifBtn.style.display = 'flex';
        fetchCustomerNotifs();
      }
    }, 1000);

    // 5. Privacy Policy & Terms Modal (#footerPolicyModal)
    const openPrivacyModalLink = document.getElementById('openPrivacyModalLink');
    const openTermsModalLink = document.getElementById('openTermsModalLink');
    const footerPolicyModal = document.getElementById('footerPolicyModal');
    const closeFooterPolicyModal = document.getElementById('closeFooterPolicyModal');
    const closeFooterPolicyBtn = document.getElementById('closeFooterPolicyBtn');
    const footerPolicyTitle = document.getElementById('footerPolicyTitle');
    const footerPolicyContent = document.getElementById('footerPolicyContent');

    function openPolicyModal(type) {
      if (!footerPolicyModal) return;

      if (type === 'privacy') {
        if (footerPolicyTitle) footerPolicyTitle.textContent = 'Chính Sách Bảo Mật Dữ Liệu & Thông Tin';
        if (footerPolicyContent) {
          footerPolicyContent.innerHTML = `
            <h4 style="font-weight: 800; color: #0F172A; margin-top: 0;">1. Cam Kết An Toàn Thông Tin ISO 27001</h4>
            <p>Hệ thống VNPT và VNPT cam kết bảo vệ tuyệt đối thông tin dữ liệu cá nhân, thông tin doanh nghiệp theo tiêu chuẩn An toàn Thông tin Quốc tế ISO/IEC 27001:2013.</p>
            <h4 style="font-weight: 800; color: #0F172A;">2. Thu Thập Dữ Liệu Khách Hàng</h4>
            <p>Dữ liệu đăng ký tư vấn bao gồm Họ tên, Số điện thoại, Email chỉ được sử dụng cho mục đích liên hệ giải đáp, cung cấp hạ tầng dịch vụ và hỗ trợ kỹ thuật 24/7.</p>
            <h4 style="font-weight: 800; color: #0F172A;">3. Bảo Vệ Dữ Liệu Điện Toán Đám Mây</h4>
            <p>Tất cả dữ liệu truyền tải qua cổng VNPT đều được mã hóa bằng giao thức SSL/TLS 1.3 và lưu trữ tại hệ thống Data Center Tier III chống truy cập trái phép.</p>
          `;
        }
      } else {
        if (footerPolicyTitle) footerPolicyTitle.textContent = 'Điều Khoản Dịch Vụ Nền Tảng VNPT';
        if (footerPolicyContent) {
          footerPolicyContent.innerHTML = `
            <h4 style="font-weight: 800; color: #0F172A; margin-top: 0;">1. Quy Định Sử Dụng Dịch Vụ</h4>
            <p>Khách hàng khi sử dụng các dịch vụ Cloud, AI, 5G và Doanh nghiệp Số trên VNPT cần tuân thủ đúng quy định pháp luật Việt Nam về viễn thông và an toàn an ninh mạng.</p>
            <h4 style="font-weight: 800; color: #0F172A;">2. Cam Kết Cam Kết Chất Lượng SLA 99.99%</h4>
            <p>VNPT cung cấp dịch vụ với cam kết sẵn sàng hệ thống SLA 99.99%. Trường hợp gián đoạn kỹ thuật vượt quá khung cam kết sẽ được bồi thường theo đúng thỏa thuận hợp đồng.</p>
            <h4 style="font-weight: 800; color: #0F172A;">3. Hỗ Trợ Kỹ Thuật & Tải Bản Cập Nhật</h4>
            <p>Đội ngũ kỹ sư hạ tầng VNPT hỗ trợ 24/7/365 qua Hotline miễn cước 1800 1260 và hệ thống Live Chat tự động.</p>
          `;
        }
      }

      if (typeof closeAllModals === 'function') closeAllModals();
      footerPolicyModal.style.display = 'flex';
      footerPolicyModal.classList.add('open');
      if (modalOverlay) modalOverlay.classList.add('active');
    }

    function closePolicyModal() {
      if (!footerPolicyModal) return;
      footerPolicyModal.style.display = 'none';
      footerPolicyModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    openPrivacyModalLink?.addEventListener('click', (e) => {
      e.preventDefault();
      openPolicyModal('privacy');
    });
    openTermsModalLink?.addEventListener('click', (e) => {
      e.preventDefault();
      openPolicyModal('terms');
    });

    closeFooterPolicyModal?.addEventListener('click', closePolicyModal);
    closeFooterPolicyBtn?.addEventListener('click', closePolicyModal);

    // 6. Security Badges (#badgeISO27001, #badgeTop10ICT)
    const badgeISO27001 = document.getElementById('badgeISO27001');
    const badgeTop10ICT = document.getElementById('badgeTop10ICT');
    const footerBadgeModal = document.getElementById('footerBadgeModal');
    const closeFooterBadgeModal = document.getElementById('closeFooterBadgeModal');
    const closeBadgeBtn = document.getElementById('closeBadgeBtn');
    const badgeModalTitle = document.getElementById('badgeModalTitle');
    const badgeModalSub = document.getElementById('badgeModalSub');
    const badgeModalBody = document.getElementById('badgeModalBody');

    function openBadgeModal(type) {
      if (!footerBadgeModal) return;

      if (type === 'iso') {
        if (badgeModalTitle) badgeModalTitle.textContent = 'Chứng Nhận Bảo Mật ISO/IEC 27001:2013';
        if (badgeModalSub) badgeModalSub.textContent = 'Tiêu chuẩn Quốc tế về Hệ thống Quản lý An toàn Thông tin';
        if (badgeModalBody) {
          badgeModalBody.innerHTML = `
            <div style="background: #F0FDF4; border: 1px solid #BBF7D0; padding: 18px; border-radius: 16px; margin-bottom: 16px; color: #166534;">
              <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px;">🛡️ Chứng Nhận Quốc Tế ISO 27001</div>
              <p style="font-size: 0.88rem; margin: 0; line-height: 1.5;">Hạ tầng Trung tâm Dữ liệu VNPT Cloud &amp; Hệ sinh thái VNPT đạt chứng nhận an toàn thông tin ISO 27001 do Tổ chức Giám định Quốc tế BSI cấp.</p>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; font-size: 0.88rem; color: #475569;">
              <li>✔ Mã số chứng nhận: IS 689241</li>
              <li>✔ Phạm vi: Toàn bộ Trung tâm Data Center &amp; Hạ tầng Điện toán Đám mây</li>
              <li>✔ Giám sát an ninh mạng 24/7 bởi Trung tâm SOC VNPT Cyber Security</li>
            </ul>
          `;
        }
      } else {
        if (badgeModalTitle) badgeModalTitle.textContent = 'Top 10 Doanh Nghiệp ICT Việt Nam';
        if (badgeModalSub) badgeModalSub.textContent = 'Danh hiệu Doanh nghiệp Hạ tầng Số &amp; Viễn thông Hàng đầu';
        if (badgeModalBody) {
          badgeModalBody.innerHTML = `
            <div style="background: #FEFCE8; border: 1px solid #FEF08A; padding: 18px; border-radius: 16px; margin-bottom: 16px; color: #854D0E;">
              <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px;">🏆 Bằng Khen Top 10 Doanh Nghiệp Công Nghệ</div>
              <p style="font-size: 0.88rem; margin: 0; line-height: 1.5;">Hiệp hội Phần mềm và Dịch vụ CNTT Việt Nam (VINASA) trao tặng danh hiệu Top 10 Doanh nghiệp Hạ tầng Số &amp; Điện toán Đám mây 10 năm liên tiếp.</p>
            </div>
            <ul style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; font-size: 0.88rem; color: #475569;">
              <li>🥇 Doanh nghiệp Viễn thông &amp; Công nghệ Số Tiên phong 2024</li>
              <li>🥇 Giải thưởng Make in Viet Nam xuất sắc cho Nền tảng VNPT Cloud</li>
            </ul>
          `;
        }
      }

      if (window.lucide) window.lucide.createIcons();

      if (typeof closeAllModals === 'function') closeAllModals();
      footerBadgeModal.style.display = 'flex';
      footerBadgeModal.classList.add('open');
      if (modalOverlay) modalOverlay.classList.add('active');
    }

    function closeBadgeModal() {
      if (!footerBadgeModal) return;
      footerBadgeModal.style.display = 'none';
      footerBadgeModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    badgeISO27001?.addEventListener('click', () => openBadgeModal('iso'));
    badgeTop10ICT?.addEventListener('click', () => openBadgeModal('top10'));

    closeFooterBadgeModal?.addEventListener('click', closeBadgeModal);
    closeBadgeBtn?.addEventListener('click', closeBadgeModal);

    /* ---- Testimonials Case Study & Add Review Logic ---- */
    const reviewsData = {
      1: {
        author: 'Nguyễn Thanh Tùng',
        role: 'CTO – Công ty CP Thương mại ABC',
        avatar: 'NT',
        bg: 'linear-gradient(135deg,#0066CC,#00AAFF)',
        html: `
          <div style="background: #F0F9FF; border: 1px solid #BAE6FD; padding: 18px; border-radius: 16px; margin-bottom: 20px; color: #0369A1;">
            <div style="font-weight: 800; font-size: 1.1rem; margin-bottom: 8px;">📊 Kết Quả Tối Ưu Hạ Tầng Thực Tế:</div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; text-align: center;">
              <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 1.4rem; font-weight: 900; color: #0066CC;">-40%</div>
                <div style="font-size: 0.75rem; color: #64748B;">Chi phí hạ tầng IT</div>
              </div>
              <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 1.4rem; font-weight: 900; color: #0066CC;">3x</div>
                <div style="font-size: 0.75rem; color: #64748B;">Tốc độ ra mắt App</div>
              </div>
              <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 1.4rem; font-weight: 900; color: #00AA55;">99.99%</div>
                <div style="font-size: 0.75rem; color: #64748B;">Thời gian Uptime</div>
              </div>
            </div>
          </div>
          <h4 style="font-weight: 800; color: #0F172A; margin-bottom: 8px;">Bài Toán Doanh Nghiệp:</h4>
          <p style="margin-bottom: 14px; font-size: 0.92rem;">Trước khi chuyển đổi sang VNPT Cloud, chuỗi siêu thị ABC phải tự duy trì server vật lý tốn hàng tỷ đồng chi phí nâng cấp hàng năm và gặp sự cố nghẽn mạng trong các đợt Siêu Sale.</p>
          <h4 style="font-weight: 800; color: #0F172A; margin-bottom: 8px;">Giải Pháp Triển Khai:</h4>
          <p style="margin-bottom: 0; font-size: 0.92rem;">VNPT đã tự động hóa quy trình Auto-scaling cho phép cụm Cloud Server tự tăng gấp 5 lần công suất xử lý chỉ trong 3 giây khi lưu lượng người mua đột biến, giúp hệ thống vận hành hoàn hảo không gián đoạn.</p>
        `
      },
      2: {
        author: 'Lê Hoàng Minh',
        role: 'Giám đốc IT – Ngân hàng XYZ',
        avatar: 'LH',
        bg: 'linear-gradient(135deg,#FF6B00,#FFB347)',
        html: `
          <div style="background: #FFF7ED; border: 1px solid #FFEDD5; padding: 18px; border-radius: 16px; margin-bottom: 20px; color: #C2410C;">
            <div style="font-weight: 800; font-size: 1.1rem; margin-bottom: 8px;">🛡️ Chỉ Số An Toàn Bảo Mật &amp; Tuân Thủ:</div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; text-align: center;">
              <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 1.4rem; font-weight: 900; color: #FF6B00;">100%</div>
                <div style="font-size: 0.75rem; color: #64748B;">Tuân thủ Pháp lý</div>
              </div>
              <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 1.4rem; font-weight: 900; color: #FF6B00;">&lt; 1 giây</div>
                <div style="font-size: 0.75rem; color: #64748B;">Tốc độ ký số CA</div>
              </div>
              <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 1.4rem; font-weight: 900; color: #00AA55;">0</div>
                <div style="font-size: 0.75rem; color: #64748B;">Sự cố an ninh mạng</div>
              </div>
            </div>
          </div>
          <h4 style="font-weight: 800; color: #0F172A; margin-bottom: 8px;">Bài Toán Doanh Nghiệp:</h4>
          <p style="margin-bottom: 14px; font-size: 0.92rem;">Cần xác thực hàng triệu hợp đồng tín dụng trực tuyến từ xa đáp ứng khắt khe các tiêu chuẩn của Ngân hàng Nhà nước và Bộ Công Thương.</p>
          <h4 style="font-weight: 800; color: #0F172A; margin-bottom: 8px;">Giải Pháp Triển Khai:</h4>
          <p style="margin-bottom: 0; font-size: 0.92rem;">Tích hợp giải pháp Ký số từ xa VNPT SmartCA không dùng phần cứng USB Token, kết hợp hạ tầng Mã hóa HSM đạt chuẩn FIPS 140-2 Level 3 bảo mật tuyệt đối.</p>
        `
      },
      3: {
        author: 'Phạm Văn Đức',
        role: 'CEO – Tập đoàn Sản xuất DEF',
        avatar: 'PV',
        bg: 'linear-gradient(135deg,#00AA55,#00FF88)',
        html: `
          <div style="background: #F0FDF4; border: 1px solid #BBF7D0; padding: 18px; border-radius: 16px; margin-bottom: 20px; color: #166534;">
            <div style="font-weight: 800; font-size: 1.1rem; margin-bottom: 8px;">🤖 Hiệu Quả Tự Động Hóa Nhà Máy:</div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; text-align: center;">
              <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 1.4rem; font-weight: 900; color: #00AA55;">70%</div>
                <div style="font-size: 0.75rem; color: #64748B;">Quy trình tự động hóa</div>
              </div>
              <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 1.4rem; font-weight: 900; color: #00AA55;">&gt; 2 tỷ VNĐ</div>
                <div style="font-size: 0.75rem; color: #64748B;">Tiết kiệm hàng năm</div>
              </div>
              <div style="background: white; padding: 12px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <div style="font-size: 1.4rem; font-weight: 900; color: #0066CC;">24/7</div>
                <div style="font-size: 0.75rem; color: #64748B;">Giám sát cảm biến IoT</div>
              </div>
            </div>
          </div>
          <h4 style="font-weight: 800; color: #0F172A; margin-bottom: 8px;">Bài Toán Doanh Nghiệp:</h4>
          <p style="margin-bottom: 14px; font-size: 0.92rem;">Nhà máy rộng 15ha gặp rào cản trong việc theo dõi hàng ngàn thiết bị cảm biến và mất nhiều thời gian kiểm kê dây chuyền sản xuất thủ công.</p>
          <h4 style="font-weight: 800; color: #0F172A; margin-bottom: 8px;">Giải Pháp Triển Khai:</h4>
          <p style="margin-bottom: 0; font-size: 0.92rem;">Triển khai Mạng 5G Private riêng cho nhà máy thông minh kết hợp Trợ lý AI OCR bốc tách dữ liệu nhãn mác sản phẩm và Chatbot điều hành tự động.</p>
        `
      }
    };

    const reviewDetailModal = document.getElementById('reviewDetailModal');
    const closeReviewDetailModal = document.getElementById('closeReviewDetailModal');
    const closeReviewDetailBtn = document.getElementById('closeReviewDetailBtn');

    function closeReviewDetailFunc() {
      if (!reviewDetailModal) return;
      reviewDetailModal.style.display = 'none';
      reviewDetailModal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    closeReviewDetailModal?.addEventListener('click', closeReviewDetailFunc);
    closeReviewDetailBtn?.addEventListener('click', closeReviewDetailFunc);

    // Dynamic Review Card Generator
    function createTestimonialElement(review) {
      const card = document.createElement('div');
      card.className = 'testi-card clickable-testi';
      card.setAttribute('data-id', 'custom_' + (review.id || Date.now()));
      card.title = 'Nhấp để xem chi tiết nhận xét';

      const ratingNum = parseInt(review.rating || 5, 10);
      const stars = '★'.repeat(ratingNum) + '☆'.repeat(Math.max(0, 5 - ratingNum));
      const initials = (review.name || 'KH').split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() || 'KH';

      card.innerHTML = `
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
          <div class="testi-stars">${stars}</div>
          <span style="background: rgba(0, 102, 204, 0.1); color: #0066CC; font-size: 0.72rem; font-weight: 800; padding: 3px 10px; border-radius: 10px;">${review.service || 'Hạ tầng VNPT'}</span>
        </div>
        <p>"${review.comment}"</p>
        <div class="testi-author">
          <div class="testi-avatar" style="background:linear-gradient(135deg,#0066CC,#00AAFF)">${initials}</div>
          <div>
            <strong>${review.name}</strong>
            <span>${review.role || 'Khách hàng VNPT'}</span>
          </div>
        </div>
        <div class="testi-hint">Xem chi tiết <i data-lucide="arrow-up-right" style="width:14px; height:14px;"></i></div>
      `;

      reviewsData['custom_' + (review.id || Date.now())] = {
        author: review.name,
        role: review.role || 'Khách hàng VNPT',
        avatar: initials,
        bg: 'linear-gradient(135deg,#0066CC,#00AAFF)',
        html: `
          <div style="background: #F0F9FF; border: 1px solid #BAE6FD; padding: 18px; border-radius: 16px; margin-bottom: 20px; color: #0369A1;">
            <div style="font-weight: 800; font-size: 1.1rem; margin-bottom: 6px;">⭐ Nhận Xét Thực Tế Từ Khách Hàng:</div>
            <p style="font-size: 0.95rem; margin: 0; line-height: 1.6; font-style: italic;">"${review.comment}"</p>
          </div>
          <div style="display: flex; flex-direction: column; gap: 8px; font-size: 0.9rem; color: #334155;">
            <div><strong>Khách hàng:</strong> ${review.name}</div>
            <div><strong>Doanh nghiệp/Chức danh:</strong> ${review.role || 'Đối tác VNPT'}</div>
            <div><strong>Dịch vụ sử dụng:</strong> ${review.service || 'Hạ tầng VNPT'}</div>
            <div><strong>Đánh giá:</strong> ${stars} (${ratingNum}/5 Sao)</div>
          </div>
        `
      };

      card.addEventListener('click', () => {
        const id = card.getAttribute('data-id');
        const data = reviewsData[id];
        if (!data || !reviewDetailModal) return;
        if (reviewDetailAvatar) {
          reviewDetailAvatar.textContent = data.avatar;
          reviewDetailAvatar.style.background = data.bg;
        }
        if (reviewDetailAuthor) reviewDetailAuthor.textContent = data.author;
        if (reviewDetailRole) reviewDetailRole.textContent = data.role;
        if (reviewDetailBody) reviewDetailBody.innerHTML = data.html;
        if (typeof closeAllModals === 'function') closeAllModals();
        reviewDetailModal.style.display = 'flex';
        reviewDetailModal.classList.add('open');
        if (modalOverlay) modalOverlay.classList.add('active');
      });

      return card;
    }

    // Load saved custom reviews from localStorage
    function loadCustomReviews() {
      try {
        const customReviews = JSON.parse(localStorage.getItem('vnpt_custom_reviews') || '[]');
        const grid = document.querySelector('.testi-grid');
        if (!grid) return;
        customReviews.forEach(rev => {
          const el = createTestimonialElement(rev);
          grid.prepend(el);
        });
        if (window.lucide) window.lucide.createIcons();
      } catch (_e) {}
    }
    loadCustomReviews();

    /* ---- Testimonial Slider Carousel Logic ---- */
    const testiGrid = document.getElementById('testiGrid');
    const testiPrevBtn = document.getElementById('testiPrevBtn');
    const testiNextBtn = document.getElementById('testiNextBtn');
    const testiDotsContainer = document.getElementById('testiDotsContainer');

    let currentSlide = 0;

    function updateTestiSlider() {
      if (!testiGrid) return;
      const cards = testiGrid.querySelectorAll('.testi-card');
      const totalCards = cards.length;
      const cardsPerView = window.innerWidth <= 768 ? 1 : (window.innerWidth <= 1024 ? 2 : 3);
      const maxSlide = Math.max(0, totalCards - cardsPerView);

      if (currentSlide > maxSlide) currentSlide = maxSlide;
      if (currentSlide < 0) currentSlide = 0;

      const gap = 20;
      const containerWidth = testiGrid.parentElement ? testiGrid.parentElement.offsetWidth : 1200;
      const cardWidth = (containerWidth - (gap * (cardsPerView - 1))) / cardsPerView;

      cards.forEach(card => {
        card.style.flex = `0 0 ${cardWidth}px`;
        card.style.maxWidth = `${cardWidth}px`;
      });

      const moveAmount = (cardWidth + gap) * currentSlide;
      testiGrid.style.transform = `translateX(-${moveAmount}px)`;

      if (testiDotsContainer) {
        testiDotsContainer.innerHTML = '';
        const dotsCount = maxSlide + 1;
        if (dotsCount > 1) {
          for (let i = 0; i < dotsCount; i++) {
            const dot = document.createElement('span');
            dot.style.cssText = `width: ${i === currentSlide ? '24px' : '10px'}; height: 10px; border-radius: 10px; background: ${i === currentSlide ? '#0066CC' : '#CBD5E1'}; cursor: pointer; transition: all 0.3s ease;`;
            dot.addEventListener('click', () => {
              currentSlide = i;
              updateTestiSlider();
            });
            testiDotsContainer.appendChild(dot);
          }
        }
      }
    }

    testiPrevBtn?.addEventListener('click', () => {
      const container = document.querySelector('.testi-carousel-container');
      if (container) {
        container.scrollBy({ left: -340, behavior: 'smooth' });
      }
      currentSlide--;
      updateTestiSlider();
    });

    testiNextBtn?.addEventListener('click', () => {
      const container = document.querySelector('.testi-carousel-container');
      if (container) {
        container.scrollBy({ left: 340, behavior: 'smooth' });
      }
      currentSlide++;
      updateTestiSlider();
    });

    window.addEventListener('resize', updateTestiSlider);
    setTimeout(updateTestiSlider, 150);
  }

  /* ---- Product Detail & Hardware Specs Tab Modal Handler ---- */
  /* ---- Product Detail & Hardware Specs Tab Modal Handler ---- */
  function initProductDetailModal() {
    const modal           = document.getElementById('productDetailModal');
    const closeBtnX       = document.getElementById('closeProductDetailModal');
    const closeBtnBottom  = document.getElementById('closeProductDetailBtn');
    const modalAddToCart  = document.getElementById('btnModalAddToCart');
    const modalOverlay    = document.getElementById('modalOverlay');

    const pName           = document.getElementById('pDetailName');
    const pPrice          = document.getElementById('pDetailPrice');
    const pTag            = document.getElementById('pDetailTag');
    const pImg            = document.getElementById('pDetailImg');
    const pImgWrap        = document.getElementById('pDetailImageWrap');

    const tabBtnSpecs     = document.getElementById('tabBtnSpecs');
    const tabBtnInfo      = document.getElementById('tabBtnInfo');
    const tabContentSpecs = document.getElementById('tabContentSpecs');
    const tabContentInfo  = document.getElementById('tabContentInfo');
    const pSpecsBody      = document.getElementById('pDetailSpecsBody');
    const pInfoBody       = document.getElementById('pDetailInfoBody');

    let activeProductId = null;

    // Switch Tabs Logic
    function switchTab(tabName) {
      if (tabName === 'specs') {
        tabBtnSpecs?.classList.add('active');
        tabBtnInfo?.classList.remove('active');
        if (tabContentSpecs) tabContentSpecs.style.display = 'block';
        if (tabContentInfo) tabContentInfo.style.display = 'none';
      } else {
        tabBtnInfo?.classList.add('active');
        tabBtnSpecs?.classList.remove('active');
        if (tabContentInfo) tabContentInfo.style.display = 'block';
        if (tabContentSpecs) tabContentSpecs.style.display = 'none';
      }
    }

    tabBtnSpecs?.addEventListener('click', () => switchTab('specs'));
    tabBtnInfo?.addEventListener('click', () => switchTab('info'));

    window.openProductDetailModal = function(dataOrEl) {
      let data = {};
      if (dataOrEl && (dataOrEl instanceof HTMLElement || dataOrEl.nodeType === 1)) {
        const el = dataOrEl;
        const card = el.closest('.service-card, .premium-card, .price-card, .product-card') || el;
        data = {
          id: el.getAttribute('data-id') || card?.getAttribute('data-id') || '',
          name: el.getAttribute('data-name') || card?.querySelector('h3, .price-plan-name')?.textContent.trim() || '',
          price: el.getAttribute('data-price') || card?.querySelector('.price-num, .card-price')?.textContent.trim() || '',
          type: el.getAttribute('data-type') || card?.getAttribute('data-type') || 'dich_vu_so',
          img: el.getAttribute('data-img') || card?.querySelector('img')?.src || '',
          shortDesc: el.getAttribute('data-short-desc') || card?.querySelector('p')?.textContent.trim() || '',
          fullSpecs: el.getAttribute('data-full-specs') || card?.getAttribute('data-full-specs') || '',
          fullDesc: el.getAttribute('data-full-desc') || card?.getAttribute('data-full-desc') || ''
        };
      } else {
        data = dataOrEl || {};
      }

      const id        = data.id || '';
      const name      = data.name || '';
      const price     = data.price || '';
      const type      = data.type || '';
      const img       = data.img || '';
      const shortDesc = data.shortDesc || '';
      const fullSpecs = data.fullSpecs || '';
      const fullDesc  = data.fullDesc || shortDesc;

      activeProductId = id;

      if (pName) pName.textContent = name;
      if (pPrice) pPrice.textContent = price;
      if (pTag) {
        pTag.textContent = (type === 'thiet_bi') ? '💻 Thiết bị công nghệ' : ((type === 'combo') ? '📦 Gói Combo' : '🌐 Dịch vụ số');
      }

      if (img && img.trim() !== '') {
        if (pImg) {
          pImg.src = img;
          pImg.onerror = function() {
            if (pImgWrap) pImgWrap.style.display = 'none';
          };
        }
        if (pImgWrap) pImgWrap.style.display = 'flex';
      } else {
        if (pImgWrap) pImgWrap.style.display = 'none';
      }

      const specTabsWrapper = document.querySelector('.product-spec-tabs-wrapper');

      if (type === 'thiet_bi') {
        if (specTabsWrapper) specTabsWrapper.style.display = 'flex';

        if (pSpecsBody) {
          if (fullSpecs.includes('<table') || fullSpecs.includes('specs-')) {
            pSpecsBody.innerHTML = fullSpecs;
          } else if (fullSpecs.trim().length > 0) {
            const lines = fullSpecs.split(/[\r\n]+/);
            let tableRows = '';
            lines.forEach(line => {
              line = line.trim().replace(/^[-•\*\d\.\s]+/u, '');
              if (!line) return;
              if (line.includes(':')) {
                const parts = line.split(':');
                const k = parts[0].trim();
                const v = parts.slice(1).join(':').trim();
                tableRows += `<tr><th>${k}:</th><td>${v || 'Có'}</td></tr>`;
              } else {
                tableRows += `<tr><th>Tính năng:</th><td>${line}</td></tr>`;
              }
            });
            pSpecsBody.innerHTML = `
              <div class="specs-group-block">
                <h5 class="specs-group-header"><i class="fa-solid fa-layer-group"></i> Thông số &amp; Đặc tính nổi bật</h5>
                <table class="specs-detail-table">
                  ${tableRows}
                  <tr><th>Bảo hành &amp; Cam kết:</th><td>24 tháng 1 đổi 1 chính hãng VNPT &amp; SLA 99.99%</td></tr>
                </table>
              </div>
            `;
          } else {
            pSpecsBody.innerHTML = `
              <div class="specs-group-block">
                <h5 class="specs-group-header"><i class="fa-solid fa-layer-group"></i> Thông số &amp; Đặc tính nổi bật</h5>
                <table class="specs-detail-table">
                  <tr><th>Tên sản phẩm:</th><td>${name}</td></tr>
                  <tr><th>Loại hình:</th><td>Thiết bị công nghệ chính hãng</td></tr>
                  <tr><th>Đơn vị tính &amp; Giá:</th><td>${price}</td></tr>
                  <tr><th>Mô tả chi tiết:</th><td>${shortDesc || fullSpecs}</td></tr>
                  <tr><th>Cam kết chất lượng:</th><td>Hỗ trợ kỹ thuật 24/7/365 &amp; SLA 99.99% từ VNPT</td></tr>
                </table>
              </div>
            `;
          }
        }

        if (pInfoBody) {
          pInfoBody.innerHTML = `
            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 18px; border-radius: 14px; margin-bottom: 16px;">
              <h5 style="font-weight: 800; color: #0F172A; margin-top: 0;">📌 Tổng Quan Sản Phẩm</h5>
              <p style="margin: 0; color: #334155; line-height: 1.6;">${fullDesc || shortDesc}</p>
            </div>
            <h5 style="font-weight: 800; color: #0F172A; margin-bottom: 8px;">Lợi Ích Triển Khai:</h5>
            <ul style="padding-left: 20px; color: #334155; line-height: 1.7;">
              <li>Tiết kiệm chi phí đầu tư ban đầu.</li>
              <li>Tích hợp mượt mà với hệ sinh thái Chuyển đổi số VNPT.</li>
              <li>Bảo hành chính hãng 1 đổi 1 &amp; hỗ trợ 24/7.</li>
            </ul>
          `;
        }

        switchTab('specs');

      } else {
        if (specTabsWrapper) specTabsWrapper.style.display = 'none';
        if (tabContentSpecs) tabContentSpecs.style.display = 'none';
        if (tabContentInfo) tabContentInfo.style.display = 'block';

        function formatDynamicText(text) {
          if (!text) return '';
          if (text.includes('<p>') || text.includes('<div>') || text.includes('<table')) return text;

          if (/\d+\.\s+/.test(text)) {
            const items = text.split(/(?=\d+\.\s+)/);
            let formattedHtml = '<div style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 20px;">';
            items.forEach(item => {
              item = item.trim();
              if (!item) return;
              const match = item.match(/^(\d+\.\s+[^:\.\n]+[:\.\n]?)([\s\S]*)$/);
              if (match) {
                const heading = match[1].replace(/[:\.\n]$/, '');
                const content = match[2].trim().replace(/\s*-\s*/g, '<br>• ');
                formattedHtml += `
                  <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-left: 4px solid #0066CC; padding: 14px 18px; border-radius: 0 12px 12px 0;">
                    <div style="font-weight: 800; color: #0F172A; font-size: 0.98rem; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
                      <i data-lucide="check-circle-2" style="color: #0066CC; width: 16px; height: 16px;"></i> ${heading}
                    </div>
                    <div style="color: #334155; line-height: 1.65; font-size: 0.92rem;">${content}</div>
                  </div>
                `;
              } else {
                formattedHtml += `<div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 14px 18px; border-radius: 12px; color: #334155; line-height: 1.65;">${item}</div>`;
              }
            });
            formattedHtml += '</div>';
            return formattedHtml;
          }

          if (text.length > 300) {
            const paragraphs = text.split(/[\r\n]+/);
            let paragraphsHtml = '<div style="background: #F0F9FF; border: 1px solid #BAE6FD; padding: 20px; border-radius: 16px; margin-bottom: 20px; color: #0369A1;">';
            paragraphsHtml += `<h5 style="font-weight: 800; color: #0369A1; margin-top: 0; font-size: 1.05rem; display: flex; align-items: center; gap: 8px;"><i data-lucide="info"></i> Chi Tiết Giải Pháp / Dịch Vụ:</h5>`;
            paragraphs.forEach(p => {
              if (p.trim()) {
                paragraphsHtml += `<p style="margin-bottom: 10px; line-height: 1.7; font-size: 0.94rem; color: #334155;">${p.trim().replace(/\s*-\s*/g, '<br>• ')}</p>`;
              }
            });
            paragraphsHtml += '</div>';
            return paragraphsHtml;
          }

          return `
            <div style="background: #F0F9FF; border: 1px solid #BAE6FD; padding: 20px; border-radius: 16px; margin-bottom: 20px;">
              <h5 style="font-weight: 800; color: #0369A1; margin-top: 0; font-size: 1.05rem; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="info"></i> Chi Tiết Giải Pháp / Dịch Vụ:
              </h5>
              <p style="margin: 0; color: #334155; line-height: 1.7; font-size: 0.95rem;">${text}</p>
            </div>
          `;
        }

        if (pInfoBody) {
          pInfoBody.innerHTML = `
            ${formatDynamicText(fullSpecs || shortDesc)}
            <h5 style="font-weight: 800; color: #0F172A; margin-bottom: 10px; font-size: 1.02rem;">Cam Kết SLA &amp; Đặc Điểm Nổi Bật:</h5>
            <ul style="padding-left: 20px; color: #334155; line-height: 1.8; font-size: 0.92rem;">
              <li>Khởi tạo &amp; Bàn giao hạ tầng nhanh chóng chỉ trong vài phút.</li>
              <li>Bảo mật dữ liệu tuyệt đối theo tiêu chuẩn Quốc tế ISO/IEC 27001.</li>
              <li>Hỗ trợ kỹ thuật ưu tiên chuyên sâu 24/7/365 qua Hotline 1800 1260.</li>
            </ul>
          `;
        }
      }

      if (window.lucide) window.lucide.createIcons();

      modal.style.cssText = 'display: flex !important; position: fixed; inset: 0; z-index: 9999999 !important; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(6px); align-items: center; justify-content: center; padding: 20px;';
      modal.classList.add('open');
      if (modalOverlay) modalOverlay.classList.add('active');
    };

    // Event Delegation cho toàn bộ các liên kết/nút bấm mở Modal Xem chi tiết
    document.addEventListener('click', (e) => {
      const link = e.target.closest('.open-product-detail-modal, .card-link, .btn-view-detail, [data-action="open-detail"]');
      if (!link) return;
      e.preventDefault();

      const card = link.closest('.service-card, .price-card, .premium-card, .product-card') || link;

      const id        = link.getAttribute('data-id') || card?.getAttribute('data-id') || '';
      const name      = link.getAttribute('data-name') || card?.querySelector('h3, .price-plan-name, .card-title')?.textContent.trim() || '';
      const price     = link.getAttribute('data-price') || card?.querySelector('.price-num, .card-price')?.textContent.trim() || '';
      const type      = link.getAttribute('data-type') || card?.getAttribute('data-type') || 'dich_vu_so';
      const img       = link.getAttribute('data-img') || card?.querySelector('img')?.src || '';
      const shortDesc = link.getAttribute('data-short-desc') || card?.querySelector('p')?.textContent.trim() || '';
      const fullSpecs = link.getAttribute('data-full-specs') || card?.getAttribute('data-full-specs') || '';
      const fullDesc  = link.getAttribute('data-full-desc') || card?.getAttribute('data-full-desc') || shortDesc;

      if (window.openProductDetailModal) {
        window.openProductDetailModal({ id, name, price, type, img, shortDesc, fullSpecs, fullDesc });
      }
    });

    function closeModalFunc() {
      modal.style.display = 'none';
      modal.classList.remove('open');
      if (modalOverlay) modalOverlay.classList.remove('active');
    }

    closeBtnX?.addEventListener('click', closeModalFunc);
    closeBtnBottom?.addEventListener('click', closeModalFunc);
    if (modal) {
      modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModalFunc();
      });
    }

    modalAddToCart?.addEventListener('click', () => {
      closeModalFunc();
      const cartBtn = document.querySelector(`.btn-add-cart[data-id="${activeProductId}"]`);
      if (cartBtn) {
        cartBtn.click();
      } else if (typeof openConsultation === 'function') {
        openConsultation();
      }
    });
  }

  /* ------------------------------------------------------------
   * HỆ THỐNG GỬI & HIỂN THỊ NHẬN XẾT KHÁCH HÀNG (REVIEWS / TESTIMONIALS)
   * ------------------------------------------------------------ */
  function initCustomerReviewSystem() {
    const openBtn = document.getElementById('openAddReviewBtn');
    const modal = document.getElementById('customerReviewModal');
    const closeBtnX = document.getElementById('closeCustomerReviewModal');
    const cancelBtn = document.getElementById('cancelCustomerReviewBtn');
    const form = document.getElementById('customerReviewForm');
    const alertBox = document.getElementById('reviewAlertBox');
    const testiGrid = document.getElementById('testiGrid');

    function openModal() {
      if (!modal) return;
      const user = window.VNPTAuth ? window.VNPTAuth.getCurrentUser() : null;
      if (!user) {
        const loginModal = document.getElementById('loginModal');
        const modalOverlay = document.getElementById('modalOverlay');
        if (loginModal) {
          if (typeof closeAllModals === 'function') closeAllModals();
          loginModal.style.display = 'flex';
          loginModal.classList.add('open');
          if (modalOverlay) modalOverlay.classList.add('active');
        }
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toastMsg');
        if (toast && toastMsg) {
          toastMsg.textContent = '⚠️ Vui lòng đăng nhập tài khoản để gửi nhận xét & đánh giá!';
          toast.classList.add('show');
          setTimeout(() => toast.classList.remove('show'), 4000);
        } else {
          alert('⚠️ Vui lòng đăng nhập tài khoản để gửi nhận xét & đánh giá!');
        }
        return;
      }

      const authorInput = document.getElementById('reviewAuthorName');
      if (authorInput) {
        authorInput.value = user.ho_ten || `${user.firstName || ''} ${user.lastName || ''}`.trim() || user.email || '';
      }

      modal.style.display = 'flex';
      modal.classList.add('open');
      if (window.lucide) window.lucide.createIcons();
    }

    function closeModal() {
      if (!modal) return;
      modal.style.display = 'none';
      modal.classList.remove('open');
    }

    openBtn?.addEventListener('click', openModal);
    closeBtnX?.addEventListener('click', closeModal);
    cancelBtn?.addEventListener('click', closeModal);

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();

      const user = window.VNPTAuth ? window.VNPTAuth.getCurrentUser() : null;
      if (!user) {
        closeModal();
        const loginModal = document.getElementById('loginModal');
        const modalOverlay = document.getElementById('modalOverlay');
        if (loginModal) {
          if (typeof closeAllModals === 'function') closeAllModals();
          loginModal.style.display = 'flex';
          loginModal.classList.add('open');
          if (modalOverlay) modalOverlay.classList.add('active');
        }
        alert('⚠️ Vui lòng đăng nhập tài khoản để gửi nhận xét & đánh giá!');
        return;
      }

      const submitBtn = document.getElementById('submitCustomerReviewBtn');
      submitBtn.disabled = true;

      const name = (document.getElementById('reviewAuthorName')?.value || '').trim();
      const company = (document.getElementById('reviewAuthorCompany')?.value || '').trim();
      const service = document.getElementById('reviewService')?.value || 'Cloud Enterprise';
      const rating = document.getElementById('reviewRating')?.value || '5';
      const title = (document.getElementById('reviewTitle')?.value || '').trim();
      const content = (document.getElementById('reviewContent')?.value || '').trim();

      try {
        const formData = new FormData();
        formData.append('name', name);
        formData.append('company', company);
        formData.append('service', service);
        formData.append('rating', rating);
        formData.append('title', title);
        formData.append('content', content);

        const res = await fetch('../backend/api/submit_review.php', { method: 'POST', body: formData });
        const data = await res.json();

        if (data.status === 'success') {
          if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.style.background = '#F0FDF4';
            alertBox.style.color = '#166534';
            alertBox.style.border = '1px solid #BBF7D0';
            alertBox.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i>' + data.message;
          }
          form.reset();

          // 🟢 CẬP NHẬT TRỰC TIẾP LÊN GIAO DIỆN KHÔNG CẦN F5 / LOAD LẠI TRANG
          if (typeof loadTestimonialsFromApi === 'function') {
            await loadTestimonialsFromApi();
          }
          if (typeof updateTestiSlider === 'function') {
            updateTestiSlider();
          }
          if (window.showToast) window.showToast('🎉 Đã đăng bài nhận xét và hiển thị trực tiếp!');

          setTimeout(() => {
            closeModal();
            if (alertBox) alertBox.style.display = 'none';
          }, 1500);
        } else {
          if (alertBox) {
            alertBox.style.display = 'block';
            alertBox.style.background = '#FEF2F2';
            alertBox.style.color = '#991B1B';
            alertBox.style.border = '1px solid #FECACA';
            alertBox.innerHTML = '<i class="fa-solid fa-circle-exclamation me-2"></i>' + (data.message || 'Có lỗi xảy ra.');
          }
        }
      } catch (err) {
        if (alertBox) {
          alertBox.style.display = 'block';
          alertBox.style.background = '#FEF2F2';
          alertBox.style.color = '#991B1B';
          alertBox.style.border = '1px solid #FECACA';
          alertBox.innerHTML = '⚠️ Lỗi kết nối máy chủ. Vui lòng thử lại sau.';
        }
      } finally {
        submitBtn.disabled = false;
      }
    });

    // 🟢 HÀM XÓA ĐÁNH GIÁ CỦA CHÍNH KHÁCH HÀNG (LIVE REMOVAL)
    window.deleteCustomerReview = async function(reviewId) {
      if (!reviewId) return;

      const currentUser = (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function' && window.VNPTAuth.getCurrentUser()) ||
                          (JSON.parse(localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user') || 'null'));

      if (!currentUser) {
        if (window.showToast) window.showToast('⚠️ Vui lòng đăng nhập để thực hiện thao tác xóa.', true);
        else alert('⚠️ Vui lòng đăng nhập để thực hiện thao tác xóa.');
        if (typeof window.openLoginModal === 'function') window.openLoginModal();
        return;
      }

      if (!confirm('🗑️ Bạn có chắc chắn muốn xóa nhận xét này không? Thao tác này không thể hoàn tác.')) return;

      try {
        const res = await fetch('../backend/api/delete_review.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: reviewId, user: currentUser })
        });
        const data = await res.json();

        if (data.status === 'success') {
          const card = document.querySelector(`.testi-card[data-id="${reviewId}"]`);
          if (card) {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            setTimeout(() => card.remove(), 300);
          }
          const reviewDetailModal = document.getElementById('reviewDetailModal');
          if (reviewDetailModal) {
            reviewDetailModal.style.display = 'none';
            reviewDetailModal.classList.remove('open');
          }
          if (typeof closeAllModals === 'function') closeAllModals();

          if (window.showToast) window.showToast(data.message || '🗑️ Đã xóa nhận xét thành công!');
          else alert(data.message || '🗑️ Đã xóa nhận xét thành công!');

          setTimeout(() => {
            if (typeof loadTestimonialsFromApi === 'function') loadTestimonialsFromApi();
          }, 350);
        } else {
          if (window.showToast) window.showToast('⚠️ ' + (data.message || 'Không thể xóa nhận xét.'), true);
          else alert(data.message || 'Không thể xóa nhận xét.');
        }
      } catch (err) {
        alert('Lỗi kết nối khi xóa nhận xét: ' + err.message);
      }
    };

    // Tải danh sách đánh giá đối tác từ API và đồng bộ câu trả lời phản hồi của Admin
    async function loadTestimonialsFromApi() {
      if (!testiGrid) return;
      try {
        const res = await fetch('../backend/api/testimonials.php');
        const json = await res.json();
        if (json.status === 'success' && Array.isArray(json.data) && json.data.length > 0) {
          const currentUser = (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function' && window.VNPTAuth.getCurrentUser()) ||
                              (JSON.parse(localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user') || 'null'));
          const currentKhId = currentUser ? (currentUser.khach_hang_id || currentUser.id) : null;
          const currentName = currentUser ? (currentUser.ho_ten || `${currentUser.firstName || ''} ${currentUser.lastName || ''}`.trim() || currentUser.email) : '';
          
          const email = (currentUser?.email || '').toLowerCase();
          const rawRole = (currentUser?.role || currentUser?.ten_vai_tro || currentUser?.loai_tai_khoan || '').toLowerCase();
          const isStaffOrEditor = currentUser && (
            rawRole === 'admin' || rawRole === 'quan_tri_vien' ||
            rawRole === 'bien_tap_vien' || rawRole === 'editor' ||
            rawRole === 'nhan_vien' || rawRole === 'nhan_vien_ban_hang' || rawRole === 'quan_ly' ||
            email.includes('admin') || email.includes('editor') || email.endsWith('@vnpt.vn')
          );

          testiGrid.innerHTML = json.data.map((item, idx) => {
            const isFeatured = idx % 2 === 1;
            const starsHtml = '★'.repeat(item.stars) + '☆'.repeat(5 - item.stars);
            const badgeBg = isFeatured ? 'rgba(0, 229, 255, 0.2)' : 'rgba(0, 102, 204, 0.1)';
            const badgeColor = isFeatured ? '#00E5FF' : '#0066CC';
            const grad = isFeatured ? 'linear-gradient(135deg,#FF6B00,#FFB347)' : 'linear-gradient(135deg,#0066CC,#00AAFF)';
            
            const isOwner = currentUser && (
              (currentKhId && Number(item.khach_hang_id) === Number(currentKhId)) ||
              (currentName && item.name && currentName.toLowerCase().trim() === item.name.toLowerCase().trim())
            );
            const canDelete = isOwner || isStaffOrEditor;

            return `
              <div class="testi-card ${isFeatured ? 'featured-testi' : ''} clickable-testi" data-id="${item.id}" onclick="if(window.openReviewDetailModal){ window.openReviewDetailModal(this); } return false;" title="Nhấp để xem chi tiết nhận xét &amp; phản hồi từ Admin" style="flex: 0 0 calc(33.333% - 14px); min-width: 300px; box-sizing: border-box; margin: 0; cursor: pointer; position: relative;">
                
                ${canDelete ? `
                <button class="btn-delete-review" onclick="event.stopPropagation(); window.deleteCustomerReview(${item.id});" style="position: absolute; top: 12px; right: 12px; background: #FEF2F2; color: #EF4444; border: 1px solid #FCA5A5; padding: 4px 10px; border-radius: 8px; font-size: 0.72rem; font-weight: 700; cursor: pointer; transition: 0.2s; z-index: 10;" title="Xóa đánh giá của bạn">
                  <i class="fa-solid fa-trash-can me-1"></i> Xóa
                </button>` : ''}

                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; padding-right: ${canDelete ? '60px' : '0'};">
                  <div class="testi-stars" style="color: #FFB347;">${starsHtml}</div>
                  <span style="background: ${badgeBg}; color: ${badgeColor}; font-size: 0.72rem; font-weight: 800; padding: 3px 10px; border-radius: 10px;">${escapeHtml(item.service)}</span>
                </div>
                <p style="font-size: 0.92rem; line-height: 1.55; margin-bottom: 16px;">"${escapeHtml(item.content)}"</p>

                ${item.admin_reply ? `
                <div style="background: #F0FDF4; border: 1px solid #BBF7D0; border-left: 4px solid #166534; padding: 10px 14px; border-radius: 0 10px 10px 0; margin-bottom: 14px; font-size: 0.84rem; color: #166534;">
                  <strong style="color: #15803D; display: block; margin-bottom: 2px;"><i class="fa-solid fa-reply me-1"></i> VNPT Admin phản hồi:</strong>
                  "${escapeHtml(item.admin_reply)}"
                </div>` : ''}

                <div class="testi-author">
                  <div class="testi-avatar" style="background:${grad}">${escapeHtml(item.initials)}</div>
                  <div>
                    <strong>${escapeHtml(item.name)}</strong>
                    <span>${escapeHtml(item.company)}</span>
                  </div>
                </div>
                <div class="testi-hint" style="margin-top: 12px; font-size: 0.8rem; color: #0066CC; font-weight: 700;">Xem chi tiết &amp; Phản hồi <i data-lucide="arrow-up-right" style="width:14px; height:14px;"></i></div>
              </div>
            `;
          }).join('');

          testiGrid.querySelectorAll('.clickable-testi').forEach(card => {
            card.addEventListener('click', (e) => {
              if (e.target.closest('.btn-delete-review')) return;
              window.openReviewDetailModal(card);
            });
          });
        } else if (testiGrid.children.length === 0) {
          testiGrid.innerHTML = '<div style="padding: 40px; text-align: center; color: #64748B; width: 100%;">Chưa có nhận xét hoặc phản hồi nào được phê duyệt.</div>';
        }
      } catch (_e) {}
    }

    window.loadTestimonialsFromApi = loadTestimonialsFromApi;
    loadTestimonialsFromApi();
  }

  window.openReviewDetailModal = async function(elOrId) {
    const reviewDetailModal = document.getElementById('reviewDetailModal');
    const modalOverlay = document.getElementById('modalOverlay');
    if (!reviewDetailModal) return;

    let id = null;
    let cardEl = null;

    if (typeof elOrId === 'object' && elOrId !== null) {
      cardEl = elOrId.closest ? elOrId.closest('.clickable-testi') : elOrId;
      if (cardEl) id = cardEl.getAttribute('data-id');
    } else if (elOrId) {
      id = String(elOrId);
    }

    let name = cardEl ? (cardEl.querySelector('.testi-author strong')?.textContent || 'Khách hàng VNPT') : 'Khách hàng VNPT';
    let company = cardEl ? (cardEl.querySelector('.testi-author span')?.textContent || 'Đối tác Doanh nghiệp') : 'Đối tác Doanh nghiệp';
    let service = cardEl ? (cardEl.querySelector('span')?.textContent || 'Cloud Enterprise') : 'Cloud Enterprise';
    let starsHtml = cardEl ? (cardEl.querySelector('.testi-stars')?.innerHTML || '⭐⭐⭐⭐⭐') : '⭐⭐⭐⭐⭐';
    let content = cardEl ? (cardEl.querySelector('p')?.textContent || '') : '';
    let adminReply = cardEl ? (cardEl.querySelector('div[style*="166534"]')?.textContent || '') : '';
    let adminReplyTime = '';
    let khachHangId = null;

    try {
      if (id) {
        const res = await fetch('../backend/api/testimonials.php');
        const json = await res.json();
        if (json.status === 'success' && Array.isArray(json.data)) {
          const found = json.data.find(item => String(item.id) === String(id));
          if (found) {
            name = found.name;
            company = found.company;
            service = found.service;
            starsHtml = '★'.repeat(found.stars) + '☆'.repeat(5 - found.stars);
            content = found.title ? found.title + ': ' + found.content : found.content;
            adminReply = found.admin_reply || '';
            adminReplyTime = found.admin_reply_time || found.created_at;
            khachHangId = found.khach_hang_id;
          }
        }
      }
    } catch (_e) {}

    const reviewDetailAvatar = document.getElementById('reviewDetailAvatar');
    const reviewDetailAuthor = document.getElementById('reviewDetailAuthor');
    const reviewDetailRole = document.getElementById('reviewDetailRole');
    const reviewDetailBody = document.getElementById('reviewDetailBody');

    const initials = (name || 'KH').split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() || 'KH';

    if (reviewDetailAvatar) {
      reviewDetailAvatar.textContent = initials;
      reviewDetailAvatar.style.background = 'linear-gradient(135deg,#0066CC,#00AAFF)';
    }
    if (reviewDetailAuthor) reviewDetailAuthor.textContent = name;
    if (reviewDetailRole) reviewDetailRole.textContent = company;

    const cleanContent = content.replace(/^"/, '').replace(/"$/, '').trim();

    const currentUser = (window.VNPTAuth && typeof window.VNPTAuth.getCurrentUser === 'function' && window.VNPTAuth.getCurrentUser()) ||
                        (JSON.parse(localStorage.getItem('vnpt_user') || sessionStorage.getItem('vnpt_user') || 'null'));
    const currentKhId = currentUser ? (currentUser.khach_hang_id || currentUser.id) : null;
    const currentName = currentUser ? (currentUser.ho_ten || `${currentUser.firstName || ''} ${currentUser.lastName || ''}`.trim() || currentUser.email) : '';
    
    const email = (currentUser?.email || '').toLowerCase();
    const rawRole = (currentUser?.role || currentUser?.ten_vai_tro || currentUser?.loai_tai_khoan || '').toLowerCase();
    const isStaffOrEditor = currentUser && (
      rawRole === 'admin' || rawRole === 'quan_tri_vien' ||
      rawRole === 'bien_tap_vien' || rawRole === 'editor' ||
      rawRole === 'nhan_vien' || rawRole === 'nhan_vien_ban_hang' || rawRole === 'quan_ly' ||
      email.includes('admin') || email.includes('editor') || email.endsWith('@vnpt.vn')
    );

    const isOwner = currentUser && (
      (currentKhId && Number(khachHangId) === Number(currentKhId)) ||
      (currentName && name && currentName.toLowerCase().trim() === name.toLowerCase().trim())
    );
    const canDelete = isOwner || isStaffOrEditor;

    if (reviewDetailBody) {
      reviewDetailBody.innerHTML = `
        <div style="background: #F0F9FF; border: 1px solid #BAE6FD; padding: 18px; border-radius: 16px; margin-bottom: 20px; color: #0369A1;">
          <div style="font-weight: 800; font-size: 1.05rem; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="message-square" style="width: 18px; height: 18px; color: #0066CC;"></i>
            <span>Nhận Xét Thực Tế Từ Khách Hàng:</span>
          </div>
          <p style="margin: 0; font-size: 0.95rem; font-style: italic; color: #0F172A; line-height: 1.6;">
            "${escapeHtml(cleanContent)}"
          </p>
        </div>

        <div style="background: #F8FAFC; border: 1px solid #E2E8F0; padding: 16px; border-radius: 14px; margin-bottom: 16px; font-size: 0.9rem;">
          <div style="margin-bottom: 6px;"><strong>Khách hàng:</strong> ${escapeHtml(name)}</div>
          <div style="margin-bottom: 6px;"><strong>Doanh nghiệp/Chức danh:</strong> ${escapeHtml(company)}</div>
          <div style="margin-bottom: 6px;"><strong>Dịch vụ sử dụng:</strong> ${escapeHtml(service)}</div>
          <div><strong>Đánh giá:</strong> <span style="color:#FFB347;">${starsHtml}</span></div>
        </div>

        ${adminReply ? `
        <div style="background: #F0FDF4; border: 1.5px solid #BBF7D0; border-left: 5px solid #166534; padding: 18px 20px; border-radius: 14px; margin-top: 16px; box-shadow: 0 4px 12px rgba(22,101,52,0.08);">
          <div style="font-weight: 800; color: #166534; font-size: 0.98rem; margin-bottom: 6px; display: flex; align-items: center; gap: 8px;">
            <i data-lucide="check-circle-2" style="width: 18px; height: 18px; color: #166534;"></i>
            <span>Phản Hồi Chính Thức Từ Ban Quản Trị VNPT / VNPT:</span>
          </div>
          <div style="color: #15803D; font-weight: 600; line-height: 1.65; font-size: 0.95rem;">
            "${escapeHtml(adminReply.replace(/^"/, '').replace(/"$/, '').trim())}"
          </div>
          ${adminReplyTime ? `<div style="font-size: 0.78rem; color: #64748B; margin-top: 8px;">Thời gian phản hồi: ${escapeHtml(adminReplyTime)}</div>` : ''}
        </div>` : ''}

        ${canDelete ? `
        <div style="margin-top: 20px; text-align: right;">
          <button onclick="window.deleteCustomerReview(${id});" style="background: #EF4444; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; font-size: 0.88rem; cursor: pointer; box-shadow: 0 4px 12px rgba(239,68,68,0.3); transition: 0.2s;">
            <i class="fa-solid fa-trash-can me-2"></i> Xóa đánh giá
          </button>
        </div>` : ''}
      `;
    }

    if (window.lucide) window.lucide.createIcons();

    if (typeof closeAllModals === 'function') closeAllModals();
    reviewDetailModal.style.display = 'flex';
    reviewDetailModal.style.zIndex = '9999999';
    reviewDetailModal.classList.add('open');
    if (modalOverlay) modalOverlay.classList.add('active');
  };

  document.addEventListener('click', function(e) {
    const card = e.target.closest('.clickable-testi');
    if (card && window.openReviewDetailModal) {
      window.openReviewDetailModal(card);
    }
  });

  if (document.readyState !== 'loading') {
    initMain();
    initProductDetailModal();
    initCustomerReviewSystem();
  } else {
    document.addEventListener('DOMContentLoaded', () => {
      initMain();
      initProductDetailModal();
      initCustomerReviewSystem();
    });
  }
})();
