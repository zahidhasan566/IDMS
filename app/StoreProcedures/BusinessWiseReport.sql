USE [AMSDev]
GO
/****** Object:  StoredProcedure [dbo].[sp_business_wise_report]    Script Date: 1/3/2024 8:11:14 PM ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
ALTER procedure [dbo].[sp_business_wise_report]
@pbusiness varchar(10) = ''

AS

SET NOCOUNT ON

DECLARE @business varchar(10),
@closingDate date

--set @business = @pbusiness
--set @closingDate = GETDATE()
set @business = @pbusiness
set @closingDate = getdate()

select [Business].[BusinessName] as [Business], [Advances].[AdvanceID] as [AdvanceID],
    [Advances].[Amount] as [AdvanceAmount],
    CAST(
    CASE
    WHEN Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    ELSE Advances.Amount - (SUM(Expense) + SUM(Refund))
    END as int
    ) as OutstandingAmount,
    CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 30 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 30 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '1-30',
    CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 30 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 60 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 30 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 60 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '31-60',
    CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 60 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 90 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 60 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 90 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '61-90',
    CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 90 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 180 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 90 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 180 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '91-180',
    CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 180 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 365 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 180 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) <= 365 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '181-365',
    CAST(
    CASE
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 365 AND Adjustments.AdvanceID IS NULL
    THEN Advances.Amount
    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),@closingDate) > 365 AND Adjustments.AdvanceID IS NOT NULL
    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
    ELSE 0
    END as int
    ) as '366-Above'
into #temp
from [Advances]
    left join [Particulars] on [Particulars].[ParticularId] = [Advances].[ParticularId]
    left join [Adjustments] on [Adjustments].[AdvanceID] = [Advances].[AdvanceID] and ([Adjustments].[Status] = 'Approved' and [Adjustments].[AdjustmentDate] <= @closingDate or [Adjustments].[Status] is null)
    inner join [Business] on [Business].[Business] = [Advances].[AdvanceForBusiness]
    left join [AdjustmentUpdate] on [AdjustmentUpdate].[AdvanceID] = [Advances].[AdvanceID] and [AdjustmentUpdate].[Outstanding] > 0 and [AdjustmentUpdate].[Status] = 'Approved'
where [Advances].[Status] = 'Approved'
  AND [Advances].ApprovedAt <= @closingDate
  AND ([Advances].AdvanceForBusiness = @business OR @business = '')
group by [Adjustments].[AdvanceID], [Business].[BusinessName], [Advances].[AdvanceID], [Advances].[Amount], [AdjustmentUpdate].[Outstanding], [AdjustmentUpdate].[Status], [Advances].[ApprovedAt]

SELECT *
into #ageingreport
FROM #temp
where OutstandingAmount > 0

select  a.Business,SUM(a.OutstandingAmount) as Outstanding,SUM(a.[1-30]) as [1-30],SUM(a.[31-60]) as [31-60],SUM(a.[61-90]) as [61-90],SUM(a.[91-180]) as [91-180],SUM(a.[181-365]) as [181-365],SUM(a.[366-Above]) as [366-Above]
from #ageingreport a
group by a.Business
order by a.Business

DROP TABLE #temp
DROP TABLE #ageingreport


-- exec sp_business_wise_report ''