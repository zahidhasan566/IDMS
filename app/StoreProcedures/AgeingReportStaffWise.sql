ALTER procedure sp_ageing_report_staff_wise
@pclosingDate date,
@ptake int,
@pskip int,
@pexport varchar(1) = 'N',
@padvanceId varchar(10) = '',
@presStaffId varchar(10) = ''

AS

SET NOCOUNT ON

DECLARE @closingDate date,
@take int,
@offset int,
@export varchar(1),
@advanceId varchar(10),
@resStaffId varchar(10)

set @closingDate = @pclosingDate
set @take = @ptake
set @offset = @pskip
set @export = @pexport
set @advanceId = @padvanceId
set @resStaffId = @presStaffId

select [Advances].[ResStaffID] as [ResponsibleStaffID], [Advances].[ResStaffName] as [ResponsibleStaffName], [Advances].[ResStaffDesignation] as [ResponsibleStaffDesignation], [Advances].[ResStaffDepartment] as [ResponsibleStaffDepartment], [Business].[BusinessName] as [Business], [Advances].[CreatedBy] as [RequesterStaffID], [Advances].[RequisitionID] as [RequisitionID], [Advances].[AdvanceID] as [AdvanceID], CAST(
    CASE
    WHEN CONVERT(INT,Advances.IsAdminEntry) = 1
    THEN FORMAT(Advances.ApprovedAt,'dd-MM-yyyy')
    ELSE FORMAT(Advances.CreatedAt,'dd-MM-yyyy')
    END as VARCHAR(30)
    ) as RequisitionDate, FORMAT(Advances.AdjustmentDueDate,'dd-MM-yyyy') as AdjustmentDueDate, [Advances].[Amount] as [AdvanceAmount], CAST(
    CASE
    WHEN SUM(Adjustments.Expense) IS NULL
    THEN 0
    ELSE SUM(Adjustments.Expense)
    END as int
    ) as AdjustmentAmount, CAST(
    CASE
    WHEN SUM(Adjustments.Refund) IS NULL
    THEN 0
    ELSE SUM(Adjustments.Refund)
    END as int
    ) as RefundAmount, CAST(
    CASE
    WHEN Adjustments.AdvanceID IS NULL
    THEN 0
    ELSE (SUM(Expense) + SUM(Refund))
    END as int
    ) as TotalAdjustment, CAST(
    CASE
    WHEN Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    ELSE Advances.Amount - (SUM(Expense) + SUM(Refund))
    END as int
    ) as OutstandingAmount, FORMAT(Advances.ApprovedAt,'yyyy-MM-dd') as ApprovedDate, CASE WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 0 THEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) ELSE 0 END as AgeInDays, CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.AdjustmentDueDate),@closingDate) >= 0
    THEN DATEDIFF(day,Advances.AdjustmentDueDate,@closingDate)
    ELSE 0
    END as int
    ) as DaysOver, [Advances].[Purpose] as [PurposeOfAdvance], [Particulars].[Particular], CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 30 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 30 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '1-30', CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 30 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 60 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 30 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 60 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '31-60', CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 60 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 90 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 60 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 90 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '61-90', CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 90 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 180 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 90 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 180 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '91-180', CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 180 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 365 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 180 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 365 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '181-365', CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 365 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 365 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '366-Above', CAST(
    CASE
    WHEN Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    ELSE Advances.Amount - (SUM(Expense) + SUM(Refund))
    END as int
    ) as TotalAmount
into #ageingreport
from [Advances]
    left join [Particulars] on [Particulars].[ParticularId] = [Advances].[ParticularId]
    left join [Adjustments] on [Adjustments].[AdvanceID] = [Advances].[AdvanceID] and ([Adjustments].[Status] = 'Approved' and [Adjustments].[AdjustmentDate] <= @closingDate or [Adjustments].[Status] is null)
    inner join [Business] on [Business].[Business] = [Advances].[AdvanceForBusiness]
    left join [AdjustmentUpdate] on [AdjustmentUpdate].[AdvanceID] = [Advances].[AdvanceID] and [AdjustmentUpdate].[Outstanding] > 0 and [AdjustmentUpdate].[Status] = 'Approved'
where [Advances].[Status] = 'Approved' AND [Advances].ResStaffID = @resStaffId
  AND [Advances].ApprovedAt <= @closingDate
  AND ([Advances].AdvanceID = @advanceId OR @advanceId = '')
group by [Adjustments].[AdvanceID], [Business].[BusinessName], [Advances].[IsAdminEntry], [Advances].[ResStaffDesignation], [Advances].[ResStaffDepartment], [AdjustmentUpdate].[PrevAdjustment], [AdjustmentUpdate].[PrevOutstanding], [Advances].[RequisitionID], [Advances].[AdvanceID], [Advances].[CreatedBy], [Advances].[ResStaffID], [Advances].[ResStaffName], [Advances].[AdjustmentDueDate], [Advances].[Purpose], [Particulars].[Particular], [Advances].[Amount], [AdjustmentUpdate].[Adjustment], [AdjustmentUpdate].[Outstanding], [AdjustmentUpdate].[Status], [Advances].[CreatedAt], [Advances].[ApprovedAt] order by [Advances].[ResStaffID] asc, [RequisitionDate] asc

DECLARE @TotalRowCount INT
select @TotalRowCount = count(*)
from #ageingreport

if @export = 'Y'
BEGIN
select ResponsibleStaffID,ResponsibleStaffName,ResponsibleStaffDesignation,ResponsibleStaffDepartment,Business,RequesterStaffID,RequisitionID,AdvanceID,
       RequisitionDate,AdjustmentDueDate,AdvanceAmount,AdjustmentAmount,RefundAmount,TotalAdjustment,
	   CASE
	   WHEN OutstandingAmount < 0
	   THEN 0
	   ELSE
	   OutstandingAmount
	   END as OutstandingAmount
	   ,FORMAT(CONVERT(date,ApprovedDate),'dd-MM-yyyy') as ApprovedDate,AgeInDays,
       CAST(CASE WHEN DaysOver > AgeInDays THEN AgeInDays ELSE DaysOver END as VARCHAR(20)) as DaysOver,PurposeOfAdvance,Particular,[1-30],[31-60],[61-90],[91-180],[181-365],[366-Above],TotalAmount,
    @TotalRowCount AS CountData
from #ageingreport
order by convert(date,ApprovedDate) asc
END
ELSE
BEGIN
select ResponsibleStaffID,ResponsibleStaffName,ResponsibleStaffDesignation,ResponsibleStaffDepartment,Business,RequesterStaffID,RequisitionID,AdvanceID,
       RequisitionDate,AdjustmentDueDate,AdvanceAmount,AdjustmentAmount,RefundAmount,TotalAdjustment,
	   CASE
	   WHEN OutstandingAmount < 0
	   THEN 0
	   ELSE
	   OutstandingAmount
	   END as OutstandingAmount
	   ,FORMAT(CONVERT(date,ApprovedDate),'dd-MM-yyyy') as ApprovedDate,AgeInDays,
       CAST(CASE WHEN DaysOver > AgeInDays THEN AgeInDays ELSE DaysOver END as VARCHAR(20)) as DaysOver,PurposeOfAdvance,Particular,[1-30],[31-60],[61-90],[91-180],[181-365],[366-Above],TotalAmount,
    @TotalRowCount AS CountData
from #ageingreport
order by convert(date,ApprovedDate) asc
offset @offset rows
    fetch next @take rows only
END


DROP TABLE #ageingreport


-- exec sp_ageing_report_staff_wise '2023-09-11',10,0,'Y','','27129'