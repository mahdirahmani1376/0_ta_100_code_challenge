String repoUrl = "https://source.hostiran.com/dev-team/finance-service.git"
String helmRepo = "https://source.hostiran.com/dev-team/devops-and-infra/helm-and-yaml-group/services/php/finance-service.git"
String registryUrl = "keeper.hostiran.com"
String team="php"
String app="finance-service"
String deployment="finance-service"
String fullRegistryUrl="${registryUrl}/${team}/${app}"
String dockerfile = "./deploy/Dockerfile"
String pipresult = "ok"
String keypath = "./src/storage/"
String branch = env.BRANCH_NAME

@Library(['config', 'docker', 'deploy'])_

// DevSecOps stages
//String DDId = "7"
//devsecops(DDID: "$DDId", Dockerfile: "$Dockerfile")

            // mattermostNotifier 
            mattermostSend channel: 'hostiran-staging-cd', color: "#2A42EE", message: "started ${env.JOB_NAME} ${env.BUILD_NUMBER} (<${env.BUILD_URL}|Open>) and RESULT was ${currentBuild.result} " , text: "BUILD WAS started "

timestamps {
node ('public') {
    try{

    environment="release"
    if(branch.matches("release")){
        environment="release"
        replicas="1"
	      dockerfile="./deploy/Dockerfile"
        jobs_path="../deploy/jobs.yml"
        AppDomain="finance-service.cluster.hostiran.com"
        type="ImplementationSpecific"
        prefix="/"
    }else if( branch.matches("master")) {
       environment="master"
       replicas="2"
       dockerfile="./deploy/Dockerfile"
       AppDomain="finance-service-prod.cluster.hostiran.com"
       type="ImplementationSpecific"
       prefix="/"

    }


    
    stage('GIT'){
        git branch: "${branch}", credentialsId: 'Gitlab', url: "$repoUrl"
    }

    parallel sonar: {

      stage('SonarQube Analysis') {
                  catchError(buildResult: 'SUCCESS', stageResult: 'FAILURE') {
              		sonarQube("$app-$team-$environment")
                  	}
		  }
    }, deploy: {
            if( branch.matches("master")) {
              skipped="true"
              timeout(time: 1, unit: 'DAYS') {
              input(message: "DEPLOY ${env.JOB_NAME} on branch MASTER?", ok: 'DEPLOY', submitter: "farhad")
              }
              skipped="false"
            }
//    stage('Init') {
//        // getAppconfig(String app, String env="staging", String envFileName=".env")
 //       getAppconfig("$deployment", "$environment", ".env")
//    }
    stage('Build Image') {
            // dockerBuild(registryUrl: String, Tag: String, String ENV="", , extraArgs: String, dockerfilePath: String )
            dockerBuild( "$fullRegistryUrl","$BUILD_NUMBER", "-$environment", "--add-host=keeper.hostiran.com:172.29.43.203 --add-host=github.com:172.29.0.14 --add-host=api.github.com:172.29.0.14  --add-host=codeload.github.com:172.29.0.14 --add-host=deb.debian.org:172.29.0.14", "$dockerfile")
    }

    stage('deploy') {

            // run Container
            runInHostGroup("kubectl-$environment-1"){
                git branch: "$environment", credentialsId: 'Gitlab', url: "$helmRepo"

                //namespaceInit(String namespace, String env)
                namespaceInit("$app", "$environment")
                // getAppconfig(String app, String env="staging", String envFileName=".env")
                getAppconfig("$deployment", "$environment", ".env")
                // createConfig(String namespace, String env="staging", String app="")
                createConfig("$app", "$environment", "$deployment")
                // helmDeploywithDomain(String registryUrl, String tag,String namespace, String helmChart="./charts", String env, String domain, String prefix)
		            
          //helmDeploywithExtraArgs("$fullRegistryUrl", "$BUILD_NUMBER", "$app", "./", "$environment", "$replicas", "-f $jobs_path")                

             // helmDeploywithKong("$fullRegistryUrl", "$BUILD_NUMBER", "$app", "./", "$environment", "$AppDomain", "$prefix", "$type", "$replicas", "-f ../deploy/jobs.yml -f ../deploy/consumers.yml")
                helmDeploywithDomain("$fullRegistryUrl-$environment", "$BUILD_NUMBER", "$app", "./", "$environment", "$AppDomain", "$prefix", "$type", "$replicas")
                // To check rollout of new version
                timeout(time: 1, unit: 'MINUTES') {
                         sh "kubectl rollout status -n ${app} deployment ${app}"
                 }
                
                }
        
                    
    }
    
    }

    }
    catch(e) {
            pipresult = "FAILURE"
            throw e
    } finally {
            if(skipped == "true" && branch.matches("master")) {
        mattermostSend channel: 'hostiran-staging-cd', icon: 'https://jenkins.hostiran.com/static/10fe7c12/images/rage.svg', color: 'warning', message: "Build SKIPPED: ${env.JOB_NAME} #${env.BUILD_NUMBER} (<${env.BUILD_URL}|Link to build>)" ,text: "Build SKIPPED: ${env.JOB_NAME} ${env.BUILD_NUMBER} (<${env.BUILD_URL}|Open>)"

       }
      else if(pipresult == "FAILURE") {
        mattermostSend channel: 'hostiran-staging-cd', icon: 'https://jenkins.hostiran.com/static/10fe7c12/images/rage.svg', color: 'danger', message: "@ali.molaie @r.bajelan Build FAILED: ${env.JOB_NAME} #${env.BUILD_NUMBER} (<${env.BUILD_URL}|Link to build>)" ,text: "Build Failure: ${env.JOB_NAME} ${env.BUILD_NUMBER} (<${env.BUILD_URL}|Open>)"

       }
       else {
       mattermostSend channel: 'hostiran-staging-cd', icon: 'https://jenkins.hostiran.com/static/10fe7c12/images/svgs/logo.svg', color: 'good', message: "Build SUCCESS: ${env.JOB_NAME} #${env.BUILD_NUMBER} (<${env.BUILD_URL}|Link to build>)" ,text: "Build Success: ${env.JOB_NAME} ${env.BUILD_NUMBER} (<${env.BUILD_URL}|Open>)"


       


      }
    }
  }
}       



