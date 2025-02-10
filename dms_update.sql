-- CREATE Menus TABLE
CREATE TABLE [dbo].[Menus](
    [MenuID] [varchar](30) NOT NULL,
    [MenuName] [varchar](20) NOT NULL,
    [MenuIcon] [varchar](100) NOT NULL,
    [MenuLink] [varchar](255) NOT NULL,
    [MenuOrder] [int] NOT NULL,
    PRIMARY KEY(MenuID)
)
-- CREATE SUB MENU TABLE
CREATE TABLE [dbo].[SubMenus](
    [SubMenuID] [varchar](50) NOT NULL,
    [SubMenuName] [varchar](50) NOT NULL,
    [SubMenuLink] [varchar](100) NOT NULL,
    [SubMenuOrder] [int] NOT NULL,
    [MenuID] [varchar](30) NOT NULL,
    [Status] [tinyint] NOT NULL DEFAULT (1),
    PRIMARY KEY([SubMenuID])
)

--CREATE PERMISSION TABLE
CREATE TABLE [dbo].[SubMenuPermission](
    [SubMenuID] [varchar](500) NULL,
    [UserID] [varchar](500) NULL
)

-- pagination add -- usp_doLoadJobCardReport2
--/*
ALTER PROCEDURE [dbo].[usp_doLoadJobCardReport2]
	@vStartDate		DATETIME,
	@vEndDate		DATETIME,
	@vCustomerCode	VARCHAR(10),
	@vJobStatus		VARCHAR(20),
	@vJobTypeId		INT,
	@vPageLimit	    INT,
    @vpageNumber	VARCHAR(3)
AS
SET NOCOUNT ON
--*/

/*
DECLARE
	@vStartDate		DATETIME,
	@vEndDate		DATETIME,
	@vCustomerCode	VARCHAR(10),
	@vJobStatus		VARCHAR(20),
	@vJobTypeId		INT,
    @vPageLimit		INT,
    @vpageNumber		VARCHAR(3)

SET @vStartDate		= '2023-01-01'
SET @vEndDate		= '2023-12-31'
SET @vCustomerCode	= ''
SET @vJobStatus		= ''
SET @vJobTypeId		= ''
SET @vPageLimit		= '100'
SET @vpageNumber	= '%'
--*/

DECLARE @StartDate		DATETIME,
		@EndDate		DATETIME,
		@CustomerCode	VARCHAR(10),
		@JobStatus		VARCHAR(20),
		@JobTypeId		INT,
		@PageLimit	    INT,
		@pageNumber		VARCHAR(3)

SET @StartDate		= @vStartDate
SET @EndDate		= @vEndDate
SET @CustomerCode	= @vCustomerCode
SET @JobStatus		= @vJobStatus
SET @JobTypeId		= @vJobTypeId
SET @PageLimit	    = @vPageLimit
SET @pageNumber		= @vpageNumber

SELECT
    *
INTO #feedback
FROM (
         SELECT
             ROW_NUMBER() OVER(PARTITION BY JOBCard ORDER BY JOBCard) SL,
                 JobCard,
             CASE
                 WHEN FBQAns = 10	THEN  'Excellent'
                 WHEN FBQAns = 8		THEN  'Happy'
                 WHEN FBQAns = 6		THEN  'Average'
                 WHEN FBQAns = 4		THEN  'Poor'
                 WHEN FBQAns = 2		THEN  'Very Poor'
                 END AS Feedback
--INTO #feedback
         FROM Feedback F
                  INNER JOIN FeedbackDetail D
                             ON F.FeedbackId = D.FeedbackId
         WHERE FBQstnId = 4
           AND F.EntryDate BETWEEN DATEADD(MONTH,-1,@StartDate) AND DATEADD(MONTH,1,@EndDate)
           AND (@CustomerCode = '' OR F.ServiceCenterCode = @CustomerCode)
     ) S
WHERE SL = 1

SELECT *
INTO #jobCard
FROM tblJobCard J
WHERE JobDate BETWEEN @StartDate AND @EndDate + '23:59:59.000'
  AND (@CustomerCode	= '' OR J.ServiceCenterCode = @CustomerCode	)
  AND (@JobStatus		= '' OR J.JobStatus = @JobStatus)
  AND (@JobTypeId		= '' OR J.JobTypeId	= @JobTypeId)


SELECT
    ROW_NUMBER() OVER (ORDER BY J.Id) SL,
        CONVERT(date,Jobdate) Job_Date, J.JobCardNo AS Job_Card_No, SerialNo AS Serial_No,  J.CustomerName AS Customer_Name,
    J.MobileNo AS Mobile_No, J.Chassisno AS Chassis_no, J.Engineno AS Engine_no, Brand, Model,
    JobtypeName AS Job_Type,
    CONVERT(VARCHAR(255),FreeSScheduleID) Schedule_Title,
    J.Mileage,
    J.TimeRequired,
    ISNULL(DATEDIFF(MINUTE, J.JobStartTime, JobEndTime),0) TimeTaken,
    JOBStatus AS JOB_Status,
    ISNULL(F.Feedback,'') AS Feedback,
    --DIM.MasterCode + ' - ' + C.CustomerName
    CAST('' AS VARCHAR(255)) AS Sold_Dealar,
    J.ServiceCenterCode + ' - ' + CC.CustomerName  Service_Dealar,
    ISNULL(SUM(TotalPrice + ServiceCharge),0) AS Total_Price,
    ISNULL(SUM(CASE WHEN ItemType = 'Parts' THEN TotalPrice + ServiceCharge ELSE 0 END),0)  AS Parts_Price,
    ISNULL(SUM(CASE WHEN ItemType = 'Work' THEN TotalPrice + ServiceCharge ELSE 0 END),0)  AS Work_Price,

    CAST('' AS VARCHAR(8000)) AS Service_Pakage,

    TS.TechnicianName AS Technician_Name,
    BS.BayName as Bay_Name,
    ISNULL(DM.MechanicsName, '') AS Local_Mechanics,
    CASE WHEN YTD_status = 'Y' THEN 'Yes' ELSE 'No' END YTD_Status,
    ISNULL(YSR.Ytd_Stauts_Reason,'') as YDT_Status_Reason,
    CASE WHEN FI_Status = 'Y' THEN 'Yes' ELSE 'No' END FI_status,
    ISNULL(YSR1.Ytd_Stauts_Reason,'') as FI_Status_Reason,
    J.ServiceCenterCode,
    J.JobStartTime AS Job_Start_Time
INTO #final
FROM #jobCard J
         INNER JOIN Customer CC
                    ON J.ServiceCenterCode = CC.CustomerCode
         INNER JOIN tblJobType JT
                    ON J.JobTypeId = JT.Id
    --LEFT JOIN FreeServiceSchedule SS
    --	ON J.FreeSScheduleID = SS.FreeSScheduleID
         LEFT JOIN tblJobCardDetailSparepartWork W
                   ON W.JobCardNo = J.JobCardNo
    --LEFT JOIN DealarInvoiceDetails DID
    --	ON J.Chassisno = DID.Chassisno
    --LEFT JOIN DealarInvoiceMaster DIM
    --	ON DID.InvoiceID = DIM.InvoiceID
    --LEFT JOIN Customer C
    --	ON DIM.MasterCode = C.CustomerCode
         LEFT JOIN #Feedback F
                   ON J.JobCardNo = F.JobCard
         LEFT JOIN tblTechnicianSetup TS
                   ON J.TechnicianCode = TS.TechnicianCode AND J.ServiceCenterCode = TS.ServiceCenterCode
         LEFT JOIN tblBaySetup BS
                   ON j.BayCode=Bs.BayCode AND j.ServiceCenterCode=BS.ServiceCenterCode
         LEFT JOIN tblDealarMechanics DM
                   ON DM.MechanicsCode = J.LocalMechanicsCode AND DM.ServiceCenterCode = J.ServiceCenterCode
         LEFT JOIN YtdNoStatusReason YSR on YSR.Id=J.YTD_status_no_reason
         LEFT JOIN YtdNoStatusReason YSR1 on YSR1.Id=J.FI_status_no_reason
--WHERE JobDate BETWEEN @StartDate AND @EndDate + '23:59:59.000'
--	AND (@CustomerCode	= '' OR J.ServiceCenterCode = @CustomerCode	)
--	AND (@JobStatus		= '' OR J.JobStatus = @JobStatus)
--	AND (@JobTypeId		= '' OR J.JobTypeId	= @JobTypeId)
GROUP BY	J.Id,Jobdate, J.JobCardNo, SerialNo, J.CustomerName,
            J.MobileNo, J.Chassisno, J.Engineno, Brand, Model,
            JobtypeName, FreeSScheduleID, J.Mileage, JOBStatus, F.Feedback,
            J.TimeRequired, J.JobStartTime,JobEndTime,
            --DIM.MasterCode,C.CustomerName,
            J.ServiceCenterCode,
            CC.CustomerName, TS.TechnicianName, BS.BayName, DM.MechanicsName, YTD_Status, YSR.Ytd_Stauts_Reason, FI_status, YSR1.Ytd_Stauts_Reason

UPDATE #final SET
    Schedule_Title = S.ScheduleTitle
    FROM #final F
		INNER JOIN FreeServiceSchedule S
ON F.Schedule_Title = S.FreeSScheduleID

UPDATE #final SET Schedule_Title = Job_Type WHERE Schedule_Title = '0'

UPDATE #final SET
    Sold_Dealar = C.CustomerCode + ' - ' + C.CustomerName
    FROM #final F
	INNER JOIN DealarInvoiceDetails D
ON F.Chassis_no = D.ChassisNo
    INNER JOIN DealarInvoiceMaster M
    ON D.InvoiceID = M.InvoiceID
    INNER JOIN Customer C
    ON M.MasterCode = C.CustomerCode

SELECT
    f.Job_Card_No, W.WorkName
INTO #worklist
FROM tblJobCardDetailSparepartWork T
         INNER JOIN tblWorkSetup W
                    ON T.ItemCode = WorkCode
         INNER JOIN #final F
                    ON T.JobCardNo = F.Job_Card_No
                        AND F.ServiceCenterCode = W.ServiceCenterCode
WHERE T.ItemType = 'Work'
  AND job_type <> 'Free Service'
  and Work_Price > 0


SELECT D.JobCardNo, D.ProblemDetailsName
INTO #temp
FROM #final F
         INNER JOIN tblJobCardProblemDetails D
                    ON F.Job_Card_No = D.JobCardNo
WHERE D.ProblemDetailsName <> ''
ORDER BY 1


SELECT DISTINCT JobCardNo,
                STUFF((SELECT ', '+ ProblemDetailsName
                       FROM #temp T1
                       WHERE T1.JobCardNo = T2.JobCardNo
                    FOR XML PATH('')),1,1,'') AS Problem_Details
INTO #temp3
FROM #temp T2


SELECT DISTINCT Job_Card_No,
                STUFF((SELECT ', '+ WorkName
                       FROM #worklist T1
                       WHERE T1.Job_Card_No = T2.Job_Card_No
                    FOR XML PATH('')),1,1,'') AS Problem_Details
INTO #worklistfinal
FROM #worklist T2



ALTER TABLE #final ADD Problem_Details VARCHAR(8000) NOT NULL DEFAULT ''

--SELECT *
UPDATE #final SET
    Problem_Details = CONVERT(VARCHAR(8000),T.Problem_Details)
    FROM #final F
	INNER JOIN #temp3 T
ON F.Job_Card_No = T.JobCardNo

UPDATE #final SET
    Service_Pakage = CONVERT(VARCHAR(8000),T.Problem_Details)
    FROM #final F
	INNER JOIN #worklistfinal T
ON F.Job_Card_No = T.Job_Card_No



--SELECT Job_Card_No, count(*) FROM #final
--group by Job_Card_No
--order by 2 desc

--select * From #final
--where Job_Card_No = 'HC002320210320008'

--Paging
SELECT
    CASE WHEN @PageLimit=0 THEN 1 ELSE ((SL-1)/@PageLimit) +1 END AS PageNo, *
FROM #final
WHERE CASE WHEN @PageLimit=0 THEN 1 ELSE ((SL-1)/@PageLimit) +1 END LIKE @pageNumber
ORDER BY Sl

SELECT
    COUNT(*) NUmberOfRecord
FROM #final

--Paging


----Paging
--SELECT
--        *
--FROM #final
--WHERE CASE WHEN @PageLimit=0 THEN 1 ELSE ((SL-1)/@PageLimit) +1 END LIKE @pageNumber
--ORDER BY Sl

--SELECT
--       DISTINCT (CASE
--                                  WHEN @PageLimit=0
--                                         THEN 1
--                                  ELSE ((SL-1)/@PageLimit) +1
--                           END) AS PageNo
--FROM #final
--ORDER BY CASE WHEN @PageLimit=0 THEN 1 ELSE ((SL-1)/@PageLimit) +1 END
----Paging


DROP TABLE #feedback
DROP TABLE #final
DROP TABLE #temp
DROP TABLE #temp3
DROP TABLE #worklist
DROP TABLE #worklistfinal
DROP TABLE #jobCard

    SET NOCOUNT OFF

---end-----------------------------------------------------------------
-- pagination add -- usp_doLoadTechnicianWiseReport
ALTER PROCEDURE [dbo].[usp_doLoadTechnicianWiseReport]
	@vDateFrom			AS DATETIME,
	@vDateTo       		AS DATETIME,
	@vCustCode			AS VARCHAR(7),
	@vPageLimit	    INT,
    @vpageNumber	VARCHAR(3)
AS
SET NOCOUNT ON

/*
DECLARE
	@vDateFrom			AS DATETIME,
	@vDateTo       		AS DATETIME,
	@vCustCode			AS VARCHAR(7)
SET @vDateFrom		= '2022-05-01'
SET @vDateTo		= '2022-05-23'
SET @vCustCode		= ''
-- EXEC Usp_reportInvoice '2021-06-01','2021-06-06','HC0059','%','P','20','1',''
--*/


DECLARE
@DateFrom       AS DATETIME,
	@DateTo       	AS DATETIME,
	@CustCode       AS VARCHAR(7),
	@ProductName    AS VARCHAR(5000),
	@ReporType		AS VARCHAR(1),
	@PageLimit 		AS INT,
	@pageNumber		AS VARCHAR(3),
	@SearchValue	AS VARCHAR(50)

SET @DateFrom		= @vDateFrom
SET @DateTo       	= @vDateTo
SET @CustCode       = @vCustCode
SET @PageLimit	    = @vPageLimit
SET @pageNumber		= @vpageNumber


SELECT
    *
INTO #feedback
FROM (
         SELECT
             ROW_NUMBER() OVER(PARTITION BY JOBCard ORDER BY JOBCard) SL,
                 JobCard,
             FBQAns
--INTO #feedback
         FROM Feedback F
                  INNER JOIN FeedbackDetail D
                             ON F.FeedbackId = D.FeedbackId
         WHERE FBQstnId = 4
           AND (@CustCode = '' OR F.ServiceCenterCode = @CustCode)
     ) S
WHERE SL = 1





SELECT
    C.CustomerCode, C.CustomerName, T.BayCode, S.BayName, TC.TechnicianName,
    JobtypeName AS Job_Type,
    ISNULL(SS.ScheduleTitle, JobtypeName) Schedule_Title, F.FBQAns
    --COUNT(CASE WHEN J.Id = 8 THEN 1 ELSE NULL END) [1st Free Service],
    --COUNT(*) TotalService
INTO #final
FROM tblJobCard t
         INNER JOIN tblJobType J
                    ON T.JobTypeId = J.Id
         LEFT JOIN FreeServiceSchedule SS
                   ON t.FreeSScheduleID = SS.FreeSScheduleID
         INNER JOIN tblBaySetup S
                    ON t.ServiceCenterCode = s.ServiceCenterCode
                        AND T.BayCode = S.BayCode
         INNER JOIN tblTechnicianSetup tc
                    on t.ServiceCenterCode = tc.ServiceCenterCode AND T.TechnicianCode = tc.TechnicianCode
         INNER JOIN Customer C
                    ON T.ServiceCenterCode = C.CustomerCode
         LEFT JOIN #feedback F
                   ON F.JobCard = t.JobCardNo
WHERE JobDate BETWEEN @DateFrom AND @DateTo
  AND (@CustCode = '' OR t.ServiceCenterCode = @CustCode)
  --AND S.ServiceCenterCode IN ('HC0053','HC0054')
  --GROUP BY C.CustomerCode, C.CustomerName, T.BayCode, S.BayName, TC.TechnicianName
ORDER BY 1,2,3


UPDATE #final SET
    Job_Type = 'Free Service'
    FROM #final
WHERE Job_Type IN (
    '1st Free Service','2nd Free Service','3rd Free Service','4th Free Service','5th Free Service'
    )


UPDATE #final SET
    Job_Type = '1st Free Service'
    FROM #final
WHERE Schedule_Title = 'Free Service'



SELECT CustomerCode,CustomerName,BayCode,BayName,TechnicianName,Schedule_Title, COUNT(*) ServiceCount
INTO #ProcessData
FROM #final
GROUP BY CustomerCode,CustomerName,BayCode,BayName,TechnicianName,Schedule_Title



SELECT
    DISTINCT Schedule_Title
INTO #column
FROM #ProcessData
ORDER BY Schedule_Title



DECLARE
@query             VARCHAR(8000),
  @cols              VARCHAR(8000),
  @cols2             VARCHAR(8000),
  @columnname		VARCHAR(8000)



CREATE TABLE #output
(
    SL							INT IDENTITY(1,1),
    CustomerCode				VARCHAR(100),
    CustomerName				VARCHAR(255),
    BayCode						VARCHAR(100),
    BayName						VARCHAR(100),
    TechnicianName				VARCHAR(100)
)


SELECT @cols=  STUFF(( SELECT ','
                                  + QUOTENAME(Schedule_Title )
                       FROM #column
                         FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)') ,1,1,'')


SELECT @columnname=  STUFF(( SELECT ','
                                        +  '[' + Schedule_Title + '] VARCHAR(100) '
                             FROM #column
                               FOR XML PATH(''), TYPE).value('.', 'NVARCHAR(MAX)') ,1,1,'')
--print (@columnname)

SELECT @columnname= 'ALTER TABLE #output ADD '+ @columnname
    EXEC (@columnname)

INSERT INTO #output
    exec ('SELECT
		CustomerCode,CustomerName,BayCode,BayName,TechnicianName,
		'+@cols+'
	FROM (
		SELECT CustomerCode,CustomerName,BayCode,BayName,TechnicianName,Schedule_Title,ServiceCount
		FROM #ProcessData
	) p
	PIVOT
	(
		MAX(ServiceCount)
		FOR Schedule_Title IN ( '+@cols +' )
	)AS pvt1
		ORDER BY  CustomerCode,CustomerName,BayCode,BayName,TechnicianName
')


ALTER TABLE #output ADD TotalService	INT NOT NULL DEFAULT((0))
ALTER TABLE #output ADD Feedback		NUMERIC(18,2) NOT NULL DEFAULT((0))


SELECT CustomerCode, CustomerName, BayCode, BayName, TechnicianName, COUNT(*)  TotalService, ISNULL(AVG(FBQAns),0) AvgRating
INTO #TotalData
FROM #final
GROUP BY CustomerCode, CustomerName, BayCode, BayName, TechnicianName

UPDATE #output SET
                   TotalService	= T.TotalService,
                   Feedback		= T.AvgRating
    FROM #output O
	INNER JOIN #TotalData T
ON O.CustomerCode = T.CustomerCode
    AND O.CustomerName = T.CustomerName
    AND O.BayCode = T.BayCode
    AND O.BayName = T.BayName
    AND O.TechnicianName = T.TechnicianName



--SELECT * FROM #output

--Paging
SELECT
    CASE WHEN @PageLimit=0 THEN 1 ELSE ((SL-1)/@PageLimit) +1 END AS PageNo, *
FROM #output
WHERE CASE WHEN @PageLimit=0 THEN 1 ELSE ((SL-1)/@PageLimit) +1 END LIKE @pageNumber
ORDER BY Sl

SELECT
    COUNT(*) NUmberOfRecord
FROM #output

DROP TABLE #final
DROP TABLE #column
DROP TABLE #ProcessData
DROP TABLE #output
DROP TABLE #feedback
DROP TABLE #TotalData

    SET NOCOUNT OFF

--end
----- Job Card booking report--
ALTER PROCEDURE [dbo].[usp_doLoadJobCardBookingReport]
	@vDateFrom			AS DATETIME,
	@vDateTo       		AS DATETIME,
	@vCustCode			AS VARCHAR(7),
	@vPageLimit	    INT,
    @vpageNumber	VARCHAR(3)
AS
SET NOCOUNT ON

/*
DECLARE
	@vDateFrom			AS DATETIME,
	@vDateTo       		AS DATETIME,
	@vCustCode			AS VARCHAR(7)
SET @vDateFrom		= '2022-05-01'
SET @vDateTo		= '2022-05-23'
SET @vCustCode		= ''
-- EXEC Usp_reportInvoice '2021-06-01','2021-06-06','HC0059','%','P','20','1',''
--*/


DECLARE
@DateFrom       AS DATETIME,
	@DateTo       	AS DATETIME,
	@CustCode       AS VARCHAR(7),
	@ProductName    AS VARCHAR(5000),
	@ReporType		AS VARCHAR(1),
	@PageLimit 		AS INT,
	@pageNumber		AS VARCHAR(3),
	@SearchValue	AS VARCHAR(50)

SET @DateFrom		= @vDateFrom
SET @DateTo       	= @vDateTo
SET @CustCode       = @vCustCode
SET @PageLimit	    = @vPageLimit
SET @pageNumber		= @vpageNumber



SELECT ROW_NUMBER() OVER (ORDER BY B.CustomerName) SL,B.CustomerName AS Customer_Name, CustomerMobile AS Customer_Mobile, Chassisno AS Chassis_no,
    LEFT(ServiceDate,11) Service_Date, TimeSlot AS Time_Slot, ServiceTYpe AS Service_Type, ServiceName AS Service_Name,ReservationNo AS Reservation_No,
    ServiceCenterCode AS Service_Center_Code
into #output
FROM
    [192.168.100.201].dbYamahaServiceCenter.dbo.tblOnlineBooking B
    INNER JOIN Customer C ON B.ServiceCenterCode = C.CustomerCode
    INNER JOIN [192.168.100.201].dbYamahaServiceCenter.dbo.tblTimeSlot T ON T.TimeSlotId = B.TimeSlotId
WHERE ('' = @CustCode OR ServiceCenterCode = @CustCode)
  AND ServiceDate BETWEEN @DateFrom AND @DateTo
ORDER BY ReservationNo



--SELECT * FROM #output

--Paging
SELECT
    CASE WHEN @PageLimit=0 THEN 1 ELSE ((SL-1)/@PageLimit) +1 END AS PageNo, *
FROM #output
WHERE CASE WHEN @PageLimit=0 THEN 1 ELSE ((SL-1)/@PageLimit) +1 END LIKE @pageNumber
ORDER BY Sl

SELECT
    COUNT(*) NUmberOfRecord
FROM #output

DROP TABLE #output


    SET NOCOUNT OFF


--end------------------


--ROLE TABLE START
create table RoleList
(
    RoleId varchar(50) NOT NULL,
    RoleName varchar(50) NOT NULL,
    RoleDescription varchar(1000) default(''),
    CreatedAt datetime NOT NULL,
    UpdatedAt datetime NOT NULL,
    primary key (RoleId)
)


insert into RoleList values ('su','Super Admin','',GETDATE(),getdate())
insert into RoleList values ('admin','Admin','',GETDATE(),getdate())
insert into RoleList values ('customer','Customer','',GETDATE(),getdate())
insert into RoleList values ('tm','Territory Manager','',GETDATE(),getdate())
insert into RoleList values ('se','Support Engineer','',GETDATE(),getdate())
--ROLE TABLE END

--ROLE PERMISSION START
create table RolePermissions
(
    RoleId varchar(50) not null,
    SubMenuID varchar(50) not null,
    Active varchar(1) default('Y'),
    primary key (RoleId,SubMenuID)
)
--ROLE PERMISSION END

--TBLTECHNICIAN ADD
--TBLTECHNICIAN TRAINING LIST TABLE ADD
--TBL TECHN ICIAN TRAINING TABLE ADD
--TBL tblJobCardProblemStatement UPDATED NVARCHAR PROBLEM STATEMENT


    ---tblJobCardProblemDetails
--nvarchar  tblJobCardProblem problem name
--nvarchar tblJobCardProblemDetails problemName
-- password(255) userManager
