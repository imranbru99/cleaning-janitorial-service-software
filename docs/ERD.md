# ERD (logical)

```
Tenant 1---* Branch
Tenant 1---* User
Tenant 1---* Customer 1---* CustomerAddress
Tenant 1---* ServiceCategory 1---* Service
Customer 1---* Booking 1---* BookingItem
Booking 1---1 CleaningJob 1---* JobAssignment *---1 Employee
CleaningJob 1---* Inspection 1---* InspectionAnswer
Inspection 1---* CorrectiveAction
Booking/Contract 1---* Invoice 1---* Payment
Tenant 1---* TenantModule *--- Module registry
Tenant 1---1 Subscription *--- SubscriptionPlan
```

Quality flow: Job completed → Inspection scored → Failed creates CorrectiveAction + job status `rework_required`.
