# 📋 TASK DEPENDENCIES & EXECUTION ORDER

## 🎯 Execution Strategy
**Start Date**: 17 Desember 2025  
**Target MVP**: 30 Desember 2025 (13 hari)

---

## LEGEND
- 🔴 **CRITICAL** - Blocks other tasks
- 🟡 **HIGH** - Important for core functionality
- 🟢 **MEDIUM** - Nice to have, can be done later
- 📦 **BLOCKED BY** - Cannot start until dependencies done
- ⏰ **Est. Time** - Hours needed

---

## TASK EXECUTION PLAN

### SPRINT 1: AUTH & FOUNDATIONS (Dec 17-19 / 2-3 days)

#### Day 1: Auth System & Database Fixes
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T1.1 | Create CheckRole Middleware | 🔴 | None | 1h | ⏳ |
| T1.2 | Fix User-Role Model Relations | 🔴 | None | 1.5h | ⏳ |
| T1.3 | Update Role Model (hasMany User) | 🔴 | T1.2 | 1h | ⏳ |
| T1.4 | Add role middleware to routes | 🔴 | T1.1, T1.3 | 1.5h | ⏳ |
| T1.5 | Update navigation blade (role check) | 🟡 | T1.1 | 1h | ⏳ |
| T1.6 | Database: Verify FK constraints | 🔴 | None | 1.5h | ⏳ |
| **Total Day 1** | | | | **7.5h** | |

#### Day 2: Consumer Module Database & Models
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T2.1 | Create consumer_vehicles migration | 🔴 | None | 1h | ⏳ |
| T2.2 | Update consumers table (drop kendaraan) | 🔴 | T2.1 | 0.5h | ⏳ |
| T2.3 | Create ConsumerVehicle Model | 🔴 | T2.1 | 0.5h | ⏳ |
| T2.4 | Update Consumer Model (add vehicles relation) | 🔴 | T2.3 | 0.5h | ⏳ |
| T2.5 | Run migrations | 🔴 | T2.1 | 0.5h | ⏳ |
| T2.6 | Create Form Requests (Consumer, Vehicle) | 🟡 | T2.4 | 1.5h | ⏳ |
| **Total Day 2** | | | | **4.5h** | |

#### Day 3: Consumer Controller & Views
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T3.1 | Complete ConsumerController CRUD | 🔴 | T2.6 | 2h | ⏳ |
| T3.2 | Create ConsumerVehicleController | 🔴 | T2.6 | 1.5h | ⏳ |
| T3.3 | Create consumer views (index, create, edit, show) | 🟡 | T3.1 | 2h | ⏳ |
| T3.4 | Create vehicle views (index, create) | 🟡 | T3.2 | 1.5h | ⏳ |
| T3.5 | Add routes for consumer & vehicles | 🔴 | T3.1, T3.2 | 0.5h | ⏳ |
| T3.6 | Test all consumer CRUD operations | 🔴 | T3.5 | 1.5h | ⏳ |
| **Total Day 3** | | | | **9h** | |

**Sprint 1 Total**: ~21 hours | **Team**: 1 dev | **Buffer**: 1 day

---

### SPRINT 2: ROOM & BILLING SETUP (Dec 19-21 / 2-3 days)

#### Day 4: Room Module Complete
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T4.1 | Create StoreRoomRequest & UpdateRoomRequest | 🔴 | None | 1h | ⏳ |
| T4.2 | Update Room Model (verify all relations) | 🔴 | None | 1h | ⏳ |
| T4.3 | Refactor RoomController CRUD | 🔴 | T4.1, T4.2 | 2h | ⏳ |
| T4.4 | Create room views (index, create, edit, show) | 🟡 | T4.3 | 2h | ⏳ |
| T4.5 | Add routes for rooms | 🔴 | T4.3 | 0.5h | ⏳ |
| T4.6 | Test room CRUD operations | 🔴 | T4.5 | 1.5h | ⏳ |
| **Total Day 4** | | | | **8h** | |

#### Day 5: Billing Models & Setup
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T5.1 | Create Billing Model | 🔴 | None | 1h | ⏳ |
| T5.2 | Create BillingDetail Model | 🔴 | T5.1 | 0.5h | ⏳ |
| T5.3 | Create Payment Model | 🔴 | T5.1 | 0.5h | ⏳ |
| T5.4 | Complete RoomAddon Model | 🔴 | None | 1h | ⏳ |
| T5.5 | Verify all model relations | 🔴 | T5.1-T5.4 | 1h | ⏳ |
| T5.6 | Create Form Requests (Billing, Payment) | 🟡 | T5.1 | 1.5h | ⏳ |
| **Total Day 5** | | | | **5.5h** | |

#### Day 6: Billing Controller & Views (PARTIAL)
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T6.1 | Create BillingController | 🔴 | T5.6 | 2h | ⏳ |
| T6.2 | Create PaymentController | 🔴 | T5.6 | 1.5h | ⏳ |
| T6.3 | Create RoomAddonController | 🟡 | T5.6 | 1.5h | ⏳ |
| T6.4 | Implement auto room-status update logic | 🔴 | T6.2 | 1.5h | ⏳ |
| T6.5 | Create basic billing views | 🟡 | T6.1 | 1.5h | ⏳ |
| T6.6 | Create payment form view | 🟡 | T6.2 | 1h | ⏳ |
| **Total Day 6** | | | | **9h** | |

**Sprint 2 Total**: ~22.5 hours | **Team**: 1 dev | **Buffer**: 1 day

---

### SPRINT 3: DASHBOARD & REPORTS (Dec 22-23 / 1-2 days)

#### Day 7: Dashboard Backend
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T7.1 | Create DashboardController | 🟡 | T5.1, T4.2 | 1.5h | ⏳ |
| T7.2 | Implement revenue stat queries | 🟡 | T7.1 | 1.5h | ⏳ |
| T7.3 | Implement room stat queries | 🟡 | T7.1 | 1h | ⏳ |
| T7.4 | Add database indexes | 🔴 | None | 1.5h | ⏳ |
| T7.5 | Create API dashboard endpoints | 🟡 | T7.1-T7.3 | 1h | ⏳ |
| **Total Day 7** | | | | **6.5h** | |

#### Day 8: Dashboard Frontend
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T8.1 | Install Chart.js | 🟡 | None | 0.5h | ⏳ |
| T8.2 | Create dashboard view with stats cards | 🟡 | T7.1 | 1.5h | ⏳ |
| T8.3 | Create revenue chart (line) | 🟡 | T8.1, T7.2 | 1.5h | ⏳ |
| T8.4 | Create room status pie chart | 🟡 | T8.1, T7.3 | 1.5h | ⏳ |
| T8.5 | Add recent transactions widget | 🟡 | T7.1 | 1h | ⏳ |
| T8.6 | Test dashboard loads & charts render | 🟡 | T8.5 | 1h | ⏳ |
| **Total Day 8** | | | | **7h** | |

#### Day 9: Reports (PARTIAL)
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T9.1 | Create ReportController | 🟢 | T5.1 | 1h | ⏳ |
| T9.2 | Implement transaction report | 🟢 | T9.1 | 1h | ⏳ |
| T9.3 | Implement revenue report | 🟢 | T9.1 | 1h | ⏳ |
| T9.4 | Create report views | 🟢 | T9.1-T9.3 | 1h | ⏳ |
| T9.5 | Setup PhpSpreadsheet export | 🟢 | T9.4 | 1.5h | ⏳ |
| **Total Day 9** | | | | **5.5h** | |

**Sprint 3 Total**: ~19 hours | **Team**: 1 dev | **Buffer**: 1 day

---

### SPRINT 4: ADVANCED & FINALIZATION (Dec 24-30 / 3-4 days)

#### Day 10: WhatsApp Integration
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T10.1 | Setup WhatsApp API credentials | 🟢 | None | 1h | ⏳ |
| T10.2 | Create WhatsAppService class | 🟢 | T10.1 | 1.5h | ⏳ |
| T10.3 | Create SendWhatsAppMessage job | 🟢 | T10.2 | 1h | ⏳ |
| T10.4 | Create send invoice command | 🟢 | T6.1, T10.3 | 1.5h | ⏳ |
| T10.5 | Create send reminder command | 🟢 | T10.3 | 1.5h | ⏳ |
| T10.6 | Test WhatsApp delivery | 🟢 | T10.5 | 1h | ⏳ |
| **Total Day 10** | | | | **7.5h** | |

#### Day 11: Testing & Documentation
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T11.1 | Write unit tests (models) | 🟡 | All models | 2h | ⏳ |
| T11.2 | Write feature tests (controllers) | 🟡 | All controllers | 2.5h | ⏳ |
| T11.3 | Write authorization tests | 🔴 | T1.1-T1.4 | 1.5h | ⏳ |
| T11.4 | Run all tests & fix failures | 🔴 | T11.1-T11.3 | 2h | ⏳ |
| **Total Day 11** | | | | **8h** | |

#### Day 12: Security & Performance
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T12.1 | Security audit (input validation) | 🔴 | All features | 2h | ⏳ |
| T12.2 | SQL injection prevention check | 🔴 | All features | 1h | ⏳ |
| T12.3 | XSS prevention check | 🔴 | All features | 1h | ⏳ |
| T12.4 | CSRF tokens on all forms | 🔴 | All features | 1h | ⏳ |
| T12.5 | Performance optimization | 🟡 | All features | 2h | ⏳ |
| **Total Day 12** | | | | **7h** | |

#### Day 13: Final Testing & Deployment
| ID | Task | Priority | Depends On | Est. Time | Status |
|----|----|----------|-----------|----------|--------|
| T13.1 | End-to-end testing (all flows) | 🔴 | All features | 3h | ⏳ |
| T13.2 | Documentation update | 🟡 | All features | 1.5h | ⏳ |
| T13.3 | README & setup guide | 🟡 | All features | 1h | ⏳ |
| T13.4 | Environment setup docs | 🟡 | All features | 1h | ⏳ |
| T13.5 | Deployment checklist | 🔴 | All features | 1h | ⏳ |
| **Total Day 13** | | | | **7.5h** | |

**Sprint 4 Total**: ~29.5 hours | **Team**: 1-2 devs | **Buffer**: 2 days

---

## 📊 OVERALL SUMMARY

| Sprint | Duration | Tasks | Hours | Team |
|--------|----------|-------|-------|------|
| Sprint 1 (Auth, Consumer) | 2-3d | 21 | 21h | 1 |
| Sprint 2 (Room, Billing) | 2-3d | 21 | 22.5h | 1 |
| Sprint 3 (Dashboard, Reports) | 1-2d | 15 | 19h | 1 |
| Sprint 4 (Advanced, Final) | 3-4d | 21 | 29.5h | 1-2 |
| **TOTAL** | **13 days** | **78** | **92h** | **1-2** |

---

## 🎯 CRITICAL PATH (MUST DO IN ORDER)

```
Day 1: T1.1 → T1.2 → T1.3 → T1.4 (Auth foundation)
Day 2: T2.1 → T2.4 → T2.6 (Consumer models & validation)
Day 3: T3.1 → T3.5 (Consumer implementation)
Day 4: T4.1 → T4.3 → T4.5 (Room implementation)
Day 5: T5.1-T5.5 (Billing models)
Day 6: T6.1 → T6.4 (Billing controller & logic)
Day 7: T7.1 → T7.4 (Dashboard backend)
Day 8: T8.1 → T8.6 (Dashboard frontend)
Day 9: T9.1 → T9.5 (Reports - can be parallel)
Day 10: T10.1 → T10.6 (WhatsApp - optional before deployment)
Day 11-13: Testing & Deployment
```

---

## ⚡ FAST-TRACK OPTION (7 days MVP)

**Skip**: Reports, WhatsApp, Advanced features  
**Focus**: Auth, Consumer, Room, Billing, Dashboard only

**Timeline**:
- Day 1-2: Auth + Consumer
- Day 3: Room
- Day 4-5: Billing
- Day 6: Dashboard
- Day 7: Testing + Deploy

---

## 🚦 GO/NO-GO CRITERIA

### Before Sprint 2 (After Day 3)
- [ ] All auth working
- [ ] Consumer CRUD 100%
- [ ] All middleware protecting routes
- [ ] 0 security issues

### Before Sprint 3 (After Day 6)
- [ ] Room CRUD 100%
- [ ] Billing models created
- [ ] Payment logic working
- [ ] Room status auto-updates

### Before Sprint 4 (After Day 9)
- [ ] Dashboard shows correct data
- [ ] Reports exportable
- [ ] All tests passing
- [ ] No critical bugs

### Before Deployment (After Day 13)
- [ ] All sprints complete
- [ ] Security audit passed
- [ ] Performance acceptable
- [ ] Documentation complete
- [ ] Backup & monitoring configured

---

## 📱 RESOURCE ALLOCATION

### Option 1: Solo Developer (Recommended for MVP)
- **Total Time**: 13 days consecutive
- **Hours/Day**: 6-7 hours
- **Start**: Dec 17 | **End**: Dec 30

### Option 2: Two Developers (Parallel Work)
- **Total Time**: 8-9 days
- **Allocation**:
  - Dev 1: Auth + Consumer + Dashboard
  - Dev 2: Room + Billing + Reports
- **Start**: Dec 17 | **End**: Dec 25

### Option 3: Contractor Support (Fast Track)
- **Total Time**: 5-6 days
- **Allocation**:
  - Dev 1 (Lead): Auth + Billing + Dashboard
  - Dev 2 (Support): Consumer + Room + Views
- **Start**: Dec 17 | **End**: Dec 22

---

## 💡 OPTIMIZATION TIPS

1. **Parallel Development**
   - Start Room while Consumer views are in progress
   - Start Dashboard queries while Billing CRUD is done

2. **Template Reuse**
   - Use same form template for all CRUD
   - Create blade components for repeated sections

3. **Code Scaffolding**
   - Use Laravel generators: `php artisan make:model`, `make:controller`
   - Create templates for common patterns

4. **Testing Strategy**
   - Test as you go (not at the end)
   - Automate with phpunit
   - Use continuous testing tools

5. **Git Workflow**
   - Commit after each task
   - Use feature branches
   - Regular merges to main

---

## 📞 NEXT STEPS

1. **Day 1 Action**: Start with Task T1.1 (CheckRole Middleware)
2. **Daily Review**: Check completed items
3. **Blocker Resolution**: Address dependency issues immediately
4. **Status Update**: Update this file end of each day

---

**Questions?** Review task dependencies in table above. Each task has clear prerequisites.
