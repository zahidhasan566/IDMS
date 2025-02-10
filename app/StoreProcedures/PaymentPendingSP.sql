alter procedure sp_payment_pending
@ptake int,
@pskip int,
@prequestId varchar(10) = '',
@padvanceId varchar(10) = '',
@preqStaffId varchar(10) = '',
@presStaffId varchar(10) = '',
@pbusiness varchar(10) = '',
@pdepartment varchar(100) = '',
@ppaymentMode varchar(100) = '',
@pbank varchar(100) = ''

AS

SET NOCOUNT ON

DECLARE
@take int,
@offset int,
@requestId varchar(10),
@advanceId varchar(10),
@reqStaffId varchar(10),
@resStaffId varchar(10),
@business varchar(10),
@department varchar(100),
@paymentMode varchar(100),
@bank varchar(100)

set @take = @ptake
set @offset = @pskip
set @requestId = @prequestId
set @advanceId = @padvanceId
set @reqStaffId = @preqStaffId
set @resStaffId = @presStaffId
set @business = @pbusiness
set @department = @pdepartment
set @paymentMode = @ppaymentMode
set @bank = @pbank


CREATE TABLE #temp
(
	ResponsibleStaffID				VARCHAR(255),
	ResponsibleStaffName			VARCHAR(255),
	ResponsibleStaffDesignation		VARCHAR(255),
	ResponsibleStaffDepartment		VARCHAR(255),
	Business						VARCHAR(255),
	RequesterStaffID				VARCHAR(255),
	RequisitionID					VARCHAR(255),
	AdvanceID						VARCHAR(255),
	RequisitionDate					VARCHAR(255),
	AdjustmentDueDate				VARCHAR(255),
	AdvanceAmount					NUMERIC(18,2),
	AdjustmentAmount				NUMERIC(18,2),
	RefundAmount					NUMERIC(18,2),
	TotalAdjustment					NUMERIC(18,2),
	OutstandingAmount				NUMERIC(18,2),
	ApprovedDate					VARCHAR(255),
	AgeInDays						INT,
	DaysOver						INT,
	PurposeOfAdvance				VARCHAR(800),
	Particular						VARCHAR(800),
	[1-30]							INT,
	[31-60]							INT,
	[61-90]							INT,
	[91-180]						INT,
	[181-365]						INT,
	[366-Above]						INT,
	TotalAmount						INT,
	CountData						INT
)



DECLARE @Date DATETIME
SET @Date = CONVERT(DATE,GETDATE())

insert into #temp
exec sp_ageing_report @Date,10,1,'Y','','','','',''

select ResponsibleStaffID as ResStaffID,SUM(OutstandingAmount) as Outstanding 
into #outstanding
from #temp 
where AgeInDays > 90
group by ResponsibleStaffID

select 
	CAST(CASE 
		WHEN CONVERT(INT,ad.IsAdminEntry) = 1
        THEN FORMAT(ad.ApprovedAt,'dd-MM-yyyy')
        ELSE FORMAT(ad.CreatedAt,'dd-MM-yyyy')
        END as VARCHAR(30)
    ) as RequisitionDate,
	FORMAT(r.PaymentRequiredBy,'dd-MM-yyyy') as PaymentRequiredBy,
	ad.RequisitionID,
	ad.AdvanceID,
	ad.ResStaffID as ResponsibleStaffID,
	ad.ResStaffName as ResponsibleStaffName,
	ad.Payee as PayTo,
	ad.Purpose as PurposeOfAdvance,
	ad.Amount as AdvanceAmount,
	pm.PaymentModeName as PaymentMode,
	ad.PaymentMode as PaymentModeID,
	b.BankName as Bank,
	ad.BankID,
	ad.Status,
	outs.Outstanding as OutstandingAbove90
	into #payments
from Advances ad
join Requisitions r on r.RequisitionID = ad.RequisitionID
left join PaymentModes pm on pm.PaymentMode = ad.PaymentMode
left join Banks b on b.BankID = ad.BankID
left join #outstanding outs on outs.ResStaffID = ad.ResStaffID
where ad.Status in ('Pending','On-hold')
AND (ad.RequisitionID = @requestId OR @requestId = '')
AND (ad.AdvanceID = @advanceId OR @advanceId = '')
AND (r.CreatedBy = @reqStaffId OR @reqStaffId = '')
AND (ad.ResStaffID = @resStaffId OR @resStaffId = '')
AND (ad.AdvanceForBusiness = @business OR @business = '')
AND (ad.ResStaffDepartment = @department OR @department = '')
AND (ad.PaymentMode = @paymentMode OR @paymentMode = '')
AND (ad.BankID = @bank OR @bank = '')
order by r.RequisitionID desc
offset @offset rows
fetch next @take rows only

select * from #payments

select count(*) as CountData 
from #payments 

drop table #payments
drop table #temp
drop table #outstanding

-- exec sp_payment_pending 40,0,'','','','','','','',''