alter procedure sp_estatement
@pclosingDate date,
@pbusiness varchar(10) = '',
@pdepartment varchar(100) = '',
@pstaffId varchar(10) = ''

AS

SET NOCOUNT ON

DECLARE @closingDate date,
@business varchar(10),
@department varchar(100),
@staffId varchar(10)

set @closingDate = @pclosingDate
set @business = @pbusiness
set @department = @pdepartment
set @staffId = @pstaffId


select [Advances].[RequisitionID] as [RequisitionID], [Advances].[AdvanceID] as [AdvanceID], [Advances].[CreatedBy] as [RequesterStaffID], 
[u].[StaffName] as [RequesterStaffName], [ResStaffName],[ResStaffID],[ResStaffEmail],[ResStaffMobile],[ResStaffDepartment],[ResStaffDesignation],
CAST(CASE
	WHEN CONVERT(INT,Advances.IsAdminEntry) = 1
     THEN FORMAT(Advances.ApprovedAt,'yyyy-MM-dd')
       ELSE FORMAT(Advances.CreatedAt,'yyyy-MM-dd')
      END as DATE
) as RequisitionDate,
Advances.Amount as AdvanceAmount,
			CAST(
                CASE
                    WHEN SUM(Adjustments.Expense) IS NULL
                    THEN 0
                    ELSE SUM(Adjustments.Expense)
                END as int
            ) as AdjustmentAmount, 
			CAST(
                CASE
                    WHEN SUM(Adjustments.Refund) IS NULL
                    THEN 0
                    ELSE SUM(Adjustments.Refund)
                END as int
            ) as RefundAmount, 
			CAST(
                CASE
                    WHEN Adjustments.AdvanceID IS NULL
                    THEN Advances.Amount
                    ELSE Advances.Amount - (SUM(Expense) + SUM(Refund))
                END as int
            ) as OutstandingAmount,
			CAST(
                CASE
                    WHEN CONVERT(INT,Advances.IsAdminEntry) = 1
                    THEN FORMAT(Advances.ApprovedAt,'yyyy-MM-dd')
                    ELSE FORMAT(Vouchers.CreatedAt,'yyyy-MM-dd')
                END as Date
            ) as VoucherDate,
			CASE WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 0 
				THEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) 
				ELSE 0 
			END as AgeInDays,
			CAST(
                CASE 
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.AdjustmentDueDate),@closingDate) >= 0
                        THEN DATEDIFF(day,Advances.AdjustmentDueDate,@closingDate)
                        ELSE 0
                    END as int
                ) as DaysOver,
				Advances.Purpose as PurposeOfAdvance
			into #temp
			from [Advances] 
left join Users u on u.StaffID = Advances.CreatedBy
left join [Vouchers] on [Vouchers].[AdvanceID] = [Advances].[AdvanceID] 
left join [Adjustments] on [Adjustments].[AdvanceID] = [Advances].[AdvanceID] and ([Adjustments].[Status] = 'Approved' and [Adjustments].[AdjustmentDate] <= @closingDate or [Adjustments].[Status] is null) 
inner join [Business] on [Business].[Business] = [Advances].[AdvanceForBusiness] 
left join [AdjustmentUpdate] on [AdjustmentUpdate].[AdvanceID] = [Advances].[AdvanceID] and [AdjustmentUpdate].[Outstanding] > 0 and [AdjustmentUpdate].[Status] = 'Approved' 
where [Advances].[Status] = 'Approved'
AND [Advances].ApprovedAt <= @closingDate
AND ([Advances].ResStaffID = @staffId OR @staffId = '')
AND ([Advances].AdvanceForBusiness = @business OR @business = '')
AND ([Advances].ResStaffDepartment = @department OR @department = '')
group by Advances.IsAdminEntry,Advances.AdjustmentDueDate,AdjustmentUpdate.PrevOutstanding,AdjustmentUpdate.PrevAdjustment,
AdjustmentUpdate.Status,Advances.RequisitionID,Advances.AdvanceID,Advances.CreatedBy,u.StaffName,Advances.ResStaffID,Advances.ResStaffName,
Advances.ResStaffEmail,Advances.ResStaffMobile,Advances.ResStaffDepartment,Advances.ResStaffDesignation,Advances.Purpose,Advances.Amount,
AdjustmentUpdate.Adjustment,AdjustmentUpdate.Outstanding,Advances.ApprovedAt,Advances.CreatedAt,Vouchers.CreatedAt,Adjustments.AdvanceID

SELECT * 
into #estatement
FROM #temp
where OutstandingAmount > 0

select RequesterStaffID,RequesterStaffName,ResStaffID,ResStaffName,RequisitionID,AdvanceID,FORMAT(RequisitionDate,'dd-MM-yyyy') as RequisitionDate,AdvanceAmount,AdjustmentAmount,RefundAmount,OutstandingAmount,
FORMAT(VoucherDate,'dd-MM-yyyy') as VoucherDate,AgeInDays,CAST(CASE WHEN DaysOver > AgeInDays THEN AgeInDays ELSE DaysOver END as VARCHAR(20)) as DaysOver,PurposeOfAdvance
from #estatement
order by ResStaffID asc, convert(date,VoucherDate) asc

select count(*) as CountData 
from #estatement 

DROP TABLE #temp
DROP TABLE #estatement


-- exec sp_estatement '2023-07-12','','','11638'